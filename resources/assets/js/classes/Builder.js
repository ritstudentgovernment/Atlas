import Spot from "./Spot"
import SpotsAPI from "./api/SpotsAPI"
import CanvasBuilder from "./CanvasBuilder"

export default class Builder{

    constructor() {
        this.spots = [];
        this.legend = {};
        this.markers = [];
        this.canvasBuilder = {};
        this.spotsApi = new SpotsAPI(window.api);
    }

    getSpot(id) {
        return this.spots.filter((spot) => spot.data.id === id)[0];
    }

    removeSpot(marker) {
        marker.setMap(null);
    }

    removeLastSpot() {
        let marker = this.markers.splice(this.markers.length - 1, 1)[0];
        this.spots.splice(this.spots.length - 1, 1);
        this.removeSpot(marker);
    }

    removeSpotById(id) {
        let spot = this.getSpot(id),
            index = this.spots.indexOf(spot),
            marker = this.markers.splice(index, 1)[0];
        this.removeSpot(marker);
    }

    removeAllSpots() {
        this.markers.forEach((marker) => {
            this.removeSpot(marker);
        });
    }

    newSpot(spotData, tempSpot = false) {
        let spot = new Spot(spotData, tempSpot);
        this.spots.push(spot);
        return spot;
    }

    approveSpot(spotId) {
        let self = this;
        this.spots.filter((spot)=>spot.data.id === spotId)[0].approve(()=>{
            window.openInfoWindow.close();
            self.build(false);
        });
    }

    deleteSpot(spotId) {
        let self = this;
        window.vue.$confirm('Are you sure you want to delete this spot?', 'Warning: This action is irreversible!', {
            confirmButtonText: 'Yes, I\'m Sure!',
            confirmButtonClass: 'el-button--danger',
            cancelButtonText: 'No, that was a mistake.',
            center: true
        }).then(() => {
            self.spots.filter((spot)=>spot.data.id === spotId)[0].delete()
                .then(() => {
                    self.removeSpotById(spotId)
                });
        }).catch(() => {});
    }

    instantiateSpots(json, animateDrop = true) {

        let reference = this,
            canvasBuilder = new CanvasBuilder();

        canvasBuilder.initialize();

        this.canvasBuilder = canvasBuilder;

        json.forEach(function(spotData){

            let spot = new Spot(spotData),
                icon = spot.buildIcon(canvasBuilder),
                marker = spot.drop(false, animateDrop);

            // Add the spot to the list of spots in the builder instance
            reference.spots.push(spot);

            // Add the google maps marker to the list of markers in the builder instance
            reference.markers.push(marker);

            // Build the virtual legend map (with no duplicates)
            reference.legend[icon.category] = reference.legend[icon.category] ? reference.legend[icon.category] : {};

            if (!reference.legend[icon.category].hasOwnProperty(icon.title)) {

                reference.legend[icon.category][icon.title] = icon.url;

            }

        });

    };

    buildLegend() {

        let legend = document.getElementById('legend');
        let keys = Object.keys(this.legend).sort((a, b) => {
            return Object.keys(this.legend[a]).length < Object.keys(this.legend[b]).length;
        });

        keys.forEach((categoryKey) => {

            let categoryIcons = this.legend[categoryKey];
            let categorySection = document.createElement('div');
            let sortedCategoryIcons = Object.keys(categoryIcons).sort();

            categorySection.className = 'legendSection';
            categorySection.innerHTML = '<h3>'+categoryKey+' Spot</h3>';

            for (let sortedIconKey in sortedCategoryIcons) {

                let iconKey = sortedCategoryIcons[sortedIconKey];
                let iconURL = categoryIcons[iconKey];
                let legendRow = document.createElement('div');
                legendRow.innerHTML = '<img src="' + iconURL + '"> ' + iconKey;
                categorySection.appendChild(legendRow);

            }

            legend.appendChild(categorySection);

        });

        window.fsc.forceRender();

    };

    clearLegend() {

        this.legend = {};
        document.getElementById('legend').innerHTML = "";

    };

    build(buildLegend = true, spots = false) {

        let reference = this;

        if (!spots) {
            this.spotsApi.list().then(response => {
                let json = response.data;
                window.spotData = json;
                if (buildLegend) {
                    reference.instantiateSpots(json);
                    reference.buildLegend();
                } else {
                    reference.removeAllSpots();
                    reference.instantiateSpots(json, false);
                }

            }).catch(error => {

                console.error(error);

            });
        } else {

            reference.clearLegend();
            reference.removeAllSpots();
            reference.instantiateSpots(spots, false);
            reference.buildLegend();

        }

    };

}
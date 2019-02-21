export class CanvasBuilder {

    constructor(){

        this.canvas = document.createElement("CANVAS");
        this.context = this.canvas.getContext('2d');

    }

    setAlpha(alpha) {

        this.context.globalAlpha = alpha;

    };

    makeImage(icon, color) {
        
        let reference = this;

        function drawRectangle(color) {
            reference.context.fillStyle = color;
            reference.context.fillRect(0, 0, 300, 230);
        }
        function drawTriangle(color) {
            reference.context.fillStyle = color;
            reference.context.beginPath();
            reference.context.moveTo(150, 350);
            reference.context.lineTo(90, 230);
            reference.context.lineTo(210, 230);
            reference.context.fill();
        }

        function drawText(icon) {
            reference.context.font = '150px Arial';
            reference.context.textAlign = "center";
            reference.context.fillStyle = '#fff';
            reference.context.fillText(icon, 150, 170, 300);
        }

        drawRectangle(color);
        drawTriangle(color);
        drawText(icon);

        let image = this.canvas.toDataURL("image/png");

        this.reset();

        return image;

    };

    reset() {

        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

    };

    initialize() {

        this.canvas.height = 350;
        this.canvas.width = 300;

    };

}

window.getMeta = (metaName) => {
    let metas = document.getElementsByTagName('meta');
    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }
    return '';
};

window.markers = [];
window.icons = {};
window.openInfoWindow = false;

export class Spot {

    constructor(data){
        this.data = data;
        this.icon = null;
    }

    buildIcon(canvasBuilder) {

        if (canvasBuilder instanceof CanvasBuilder) {

            // Make the icon transparent if it is for a draggable spot
            canvasBuilder.setAlpha( (this.data.hasOwnProperty('draggable') ? 0.5 : 1) );

            let icon = this.data.type.hasOwnProperty('category') ? this.data.type.category.icon : 'S';
            let color = this.data.classification.hasOwnProperty('color') ? this.data.classification.color : 'ff7700';
            let image = canvasBuilder.makeImage(icon, color);

            this.icon = {
                url: image,
                size: new google.maps.Size(30, 35),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(15, 35),
                scaledSize: new google.maps.Size(30, 35),
                title: this.data.classification.hasOwnProperty('name') ? this.data.classification.name : 'Title',
                category: this.data.type.hasOwnProperty('category') ? this.data.type.category.name : 'Category'
            };

            return this.icon;

        }

        return false;

    };

    /**
     * Function to handle dropping a particular spot on the map
     *
     * @return google.maps.Marker
     */
    drop(autoOpen = false, animate = true) {

        function openTooltip(infowindow){
            if(openInfoWindow)openInfoWindow.close();

            window.map.setZoom(16);
            window.map.panTo(marker.getPosition());

            infowindow.open(marker.get('map'), marker);
            openInfoWindow = infowindow;
        }

        /**
         * Function to handle clicking on a particular spot and bringing up its infowindow.
         *
         * @return google.maps.InfoWindow
         */
        function addClickHandler(marker, spot){

            function getDescriptorString(spot){

                let string = '';
                if (spot.hasOwnProperty('descriptors')) {

                    for(let i = 0; i < spot.descriptors.length; i++){

                        let descriptor = spot.descriptors[i];

                        string +=
                            '<div>' +
                            '<span uk-icon="icon: '+descriptor.icon+ '; ratio: .9"></span>' +
                            '&nbsp;'+descriptor.name+': '+descriptor.pivot.value+
                            '</div>';

                    }

                }
                return string

            }

            let contentString =
                '<div class="infoWindowContentBox">'+
                '<div class="infoWindowTitle">'+
                '<div>'+spot.type.name+'</div>'+
                '<span style="background-color: #'+spot.classification.color+'">'+spot.classification.name+' '+spot.type.category.name+' spot</span>'+
                '</div>'+
                '<div class="infoWindowBody">'+
                '<div class="infoWindowIconDescriptors">'+
                getDescriptorString(spot)+
                '</div>'+
                '<p>'+spot.type.category.description+'</p>'+
                '<p>'+spot.notes+'</p>'+
                '</div>'+
                '</div>';

            let infowindow = new google.maps.InfoWindow({

                content: contentString

            });

            marker.addListener('click', function() {

                openTooltip(infowindow);

            });

            return infowindow;

        }

        let marker = new google.maps.Marker({

            position: {lat: Number(this.data.lat), lng: Number(this.data.lng)},
            animation: animate ? google.maps.Animation.DROP : null,
            map: window.map,
            icon: this.icon,
            title: this.data.type.name,
            draggable: this.data.hasOwnProperty('draggable') ? this.data.draggable : false

        });

        marker.infoWindow = addClickHandler(marker, this.data);
        markers.push(marker);

        if (autoOpen) {

            setTimeout(function(){

                openTooltip(marker.infoWindow);

            }, 400);

        }

        return marker;

    };

}

export class Builder{

    constructor(){
        this.spots = [];
        this.legend = {};
        this.markers = [];
        this.canvasBuilder = {};
    }

    removeLastSpot() {
        let marker = this.markers.splice(this.markers.length - 1, 1)[0];
        marker.setMap(null);
    }

    removeAllSpots() {
        this.markers.splice(0, this.markers.length);
    }

    newSpot(spotData) {
        return new Spot(spotData);
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
        let keys = Object.keys(this.legend).sort((a, b) => b > a);

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

    };

    build(buildLegend = true) {

        let reference = this;

        axios.get('/api/spots').then(response => {
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

    };

}

/**
 * Function google calls when the maps API is finally loaded.
 *
 * @return void.
 */
window.initMap = () => {
    /**
     * Function to show the google map at the appropriate location
     *
     * @return void.
     */
    function instantiateMap(){
        // Instantiate Google Maps on the page
        window.map = new google.maps.Map(document.getElementById('napMap'), {
            center: JSON.parse(getMeta('googleMapsCenter')),
            zoom: 16
        });
    }
    /**
     * Function to restrict the movement of the Google Map so that the user doesn't loose campus.
     *
     * @return void.
     */
    function restrictMapMovement(){

        let center = JSON.parse(getMeta('googleMapsCenter'));
        let lastValidCenter = map.getCenter();
        let allowedBounds = new google.maps.LatLngBounds(

            new google.maps.LatLng(center['min_lat'], center['min_lng']),
            new google.maps.LatLng(center['max_lat'], center['max_lng'])

        );
        google.maps.event.addListener(map, 'center_changed', () => {

            if (allowedBounds.contains(map.getCenter())) {

                lastValidCenter = map.getCenter();
                return;

            }
            map.panTo(lastValidCenter);

        });
        map.setOptions({ minZoom: 15, maxZoom: 20 });

    }

    instantiateMap();
    restrictMapMovement();
    loaded();

    window.builder = new Builder();
    builder.build();

    // Sometimes the map will fail to display, so if the tiles are not loaded after 800ms try it again.
    setTimeout(() => {

        if (window.map.tilesloading) {

            instantiateMap();
            restrictMapMovement();
            builder.instantiateSpots(window.spotData);

        }

    }, 1000);

};
import CanvasBuilder from "./CanvasBuilder";
import SpotsAPI from "./api/SpotsAPI";

export default class Spot {

    constructor(data, tempSpot = false) {
        this.data = data;
        this.icon = null;
        this.api = window.spotsApi instanceof SpotsAPI ? window.spotsApi : new SpotsAPI(window.api);
        this.tempSpot = tempSpot;
        this.marker = null;
        this.infowindow = null;
    }

    approve(callback = false) {
        if (!this.data.approved) {
            this.api.approve(this.data.id).then(() => {
                window.vue.$notify({
                    title: 'Success',
                    message: 'Spot Approved',
                    type: 'success'
                });
                if (callback) callback();
            }).catch((error) => {
                window.vue.$notify.error({
                    title: 'Error',
                    message: error.response.data.message
                });
            });
        }
    }

    delete() {
        return new Promise((resolve) => {
            this.api.delete(this.data.id).then(() => {
                resolve();
            }).catch((error) => {
                window.vue.$notify.error({
                    title: 'Error',
                    message: error.response.data.message
                });
            });
        });
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

    openTooltip() {
        if(window.openInfoWindow)window.openInfoWindow.close();

        window.map.setZoom(16);
        window.map.panTo(this.marker.getPosition());

        this.infowindow.open(this.marker.get('map'), this.marker);
        window.openInfoWindow = this.infowindow;
    }

    getDescriptorString() {

        let spot = this.data,
            string = '';

        if (spot.hasOwnProperty('descriptors')) {

            for(let i = 0; i < spot.descriptors.length; i++){

                let descriptor = spot.descriptors[i];

                string +=
                    `<div>
                        <span uk-icon="icon: ${descriptor.icon}; ratio: .9"></span>
                        &nbsp;${descriptor.name}: ${descriptor.pivot.value}
                    </div>`;

            }

        }
        return string

    }

    getAdministrativeString() {

        let user,
            string = '',
            spot = this.data;

        if ((user = getMeta('user')) && !self.tempSpot) {
            if (user.roles.length > 0) { // User is a member of at least one role (reviewer or admin currently)
                if (user.roles.some(role => (role.name === 'admin' || role.name === 'reviewer'))) {
                    string +=
                    `<div class="infoWindowAdministrative">
                        <hr />
                        <div class="center">`;
                    if (!spot.approved) {
                        string +=
                            `<button class="material-hover material-button transition approve-spot" onclick="window.builder.approveSpot(${spot.id})">
                                Approve Spot
                            </button>`;
                    }
                    string +=
                            `<button class="material-hover el-button--danger material-button transition approve-spot" onclick="window.builder.deleteSpot(${spot.id})">
                                    Delete Spot
                            </button>
                        </div>
                    <div/>`;
                }
            }
        }
        return string;

    }

    getImageString() {
        let spot = this.data;

        if (spot.image_url) {
            return `<div class="infoWindowImageContainer">
                        <img src="${spot.image_url}" alt="${spot.classification.name} ${spot.type.category.name} ${spot.type.name} Spot" />
                    </div>`;
        }

        return '';
    }

    /**
     * Function to handle clicking on a particular spot and bringing up its infowindow.
     *
     * @return google.maps.InfoWindow
     */
    addClickHandler() {

        let spot = this.data,
            contentString =
                `<div class="infoWindowContentBox">
                    <div class="infoWindowTitle">
                        <div>${spot.type.name}</div>
                        <span style="background-color: #${spot.classification.color}">${spot.classification.name} ${spot.type.category.name} spot</span>
                    </div>
                    <div class="infoWindowBody">
                        <div class="infoWindowIconDescriptors">
                            ${this.getDescriptorString()}
                        </div>
                        <p>${spot.type.category.description}</p>
                        <p>${spot.notes ? spot.notes : ''}</p>
                        ${this.getImageString()}
                        ${this.getAdministrativeString()}
                    </div>
                </div>`;

        this.infowindow = new google.maps.InfoWindow({

            content: contentString

        });

        this.marker.addListener('click', () => {

            this.openTooltip();

        });

        return this.infowindow;

    }

    /**
     * Function to handle dropping a particular spot on the map
     *
     * @return google.maps.Marker
     */
    drop(autoOpen = false, animate = true) {

        let self = this;

        this.marker = new google.maps.Marker({

            position: {lat: Number(this.data.lat), lng: Number(this.data.lng)},
            animation: animate ? google.maps.Animation.DROP : null,
            map: window.map,
            icon: this.icon,
            title: this.data.type.name,
            draggable: this.data.hasOwnProperty('draggable') ? this.data.draggable : false

        });

        this.marker.infoWindow = this.addClickHandler();
        window.markers.push(this.marker);

        if (autoOpen) {

            setTimeout(function(){

                self.openTooltip();

            }, 400);

        }

        return this.marker;

    };

}

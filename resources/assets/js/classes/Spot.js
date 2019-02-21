import CanvasBuilder from "./CanvasBuilder";

export default class Spot {

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

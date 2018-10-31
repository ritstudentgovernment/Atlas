@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/home.css') }}">
    <script src="{{ mix('js/home.js') }}"></script>

@endsection

@section("body")

    <div id="map">

        <div id="legend"></div>
        <div id="napMap"></div>

        @auth
        <button id="new-spot" class="cursor circle material-hover transition" title="Add a new spot">
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>
        @endauth

    </div>

@endsection

@section("scripts")

    <script>

        function Position(){



        }

        function Type(){



        }

        function Descriptor(){



        }

        function Spot(data){

            this.data = data;
            this.icon = null;

            this.buildIcon = function(canvasBuilder){

                if (canvasBuilder instanceof CanvasBuilder) {

                    let icon = data.type.hasOwnProperty('category') ? data.type.category.icon : 'T';
                    let color = data.classification.hasOwnProperty('color') ? data.classification.color : 'ff7700';
                    let image = canvasBuilder.makeImage(icon, color);

                    this.icon = {
                        url: image,
                        size: new google.maps.Size(30, 35),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(15, 35),
                        scaledSize: new google.maps.Size(30, 35),
                        title: data.classification.hasOwnProperty('name') ? data.classification.name : 'Title',
                        category: data.type.hasOwnProperty('category') ? data.type.category.name : 'Category'
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
            this.drop = function(){

                /**
                 * Function to handle clicking on a particular spot and bringing up its infowindow.
                 *
                 * @return google.maps.InfoWindow
                 */
                function addClickHandler(marker, spot){

                    function getDescriptorString(spot){

                        let contentString = '';
                        for(let i = 0; i < spot.descriptors.length; i++){

                            let descriptor = spot.descriptors[i];

                            contentString +=
                                '<div>' +
                                    '<span uk-icon="icon: "'+descriptor.icon+ '"; ratio: 1.1"></span>&nbsp;Quiet Level: '+
                                    spot.quietLevel+
                                '</div>';

                        }

                    }

                    let contentString =
                        '<div class="infoWindowContentBox">'+
                            '<div class="infoWindowTitle">'+
                                '<div>'+spot.name+'</div>'+
                                '<span>'+spot.classification+' '+spot.type.category.name+' spot</span>'+
                            '</div>'+
                            '<div class="infoWindowBody">'+
                                '<div class="infoWindowIconDescriptors">' +
                                    '<div>' +
                                        '<span uk-icon="icon: bell; ratio: 1.1"></span>&nbsp;Quiet Level: '+
                                        spot.quietLevel+
                                    '</div>'+
                                    '<div>' +
                                        '<span uk-icon="icon: nut; ratio: 1.1"></span>&nbsp;Spot Type: '+
                                        spot.type.name+
                                    '</div>'+
                                '</div>'+
                                '<p>'+spot.type.category.description+'</p>'+
                                '<p>'+spot.notes+'</p>'+
                            '</div>'+
                        '</div>';

                    let infowindow = new google.maps.InfoWindow({

                        content: contentString

                    });

                    marker.addListener('click', function() {

                        if(openInfoWindow)openInfoWindow.close();

                        window.map.setZoom(16);
                        window.map.panTo(marker.getPosition());

                        infowindow.open(marker.get('map'), marker);
                        openInfoWindow = infowindow;

                    });

                    return infowindow;

                }

                let reference = this;
                let marker = new google.maps.Marker({

                    position: {lat: Number(data.lat), lng:Number(data.lng)},
                    animation: google.maps.Animation.DROP,
                    map: window.map,
                    icon: reference.icon,
                    title: data.name,
                    draggable: data.hasOwnProperty('draggable') ? data.draggable : false

                });

                marker.infoWindow = addClickHandler(marker, data);
                markers.push(marker);
                return marker;

            }

        }

        function Builder(){

            this.spots = [];
            this.legend = {};
            let reference = this;

            function instantiateSpots(json){

                let canvasBuilder = new CanvasBuilder();
                canvasBuilder.initialize();

                json.forEach(function(spotData){

                    let spot = new Spot(spotData);
                    let icon = spot.buildIcon(canvasBuilder);
                    spot.drop();

                    // Add the spot to the list of spots in the builder instance
                    reference.spots.push(spot);

                    // Build the virtual legend map (with no duplicates)
                    reference.legend[icon.category] = reference.legend[icon.category] ? reference.legend[icon.category] : {};

                    if (!reference.legend[icon.category].hasOwnProperty(icon.title)) {

                        reference.legend[icon.category][icon.title] = icon.url;

                    }

                });

            }

            this.buildLegend = function(){

                let legend = document.getElementById('legend');

                for (let categoryKey in this.legend) {

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

                }

            };

            this.build = function(){

                axios.get('/api/spots').then(response => {

                    let json = response.data;
                    instantiateSpots(json);
                    reference.buildLegend();

                }).catch(error => {

                    console.error(error);

                });

            };

        }

    </script>

    <script>

        let markers = [],
            icons = {},
            openInfoWindow = false;

        /**
         * Function to sort the spots based on their classification.
         *
         * @return void.
         */
        function sortSpots(){

            spots.sort(function(a, b){

                return a.classification.charCodeAt(0) - b.classification.charCodeAt(0);

            });

        }

        /**
         * Function that gets an image object for use in a spot pin based on the classification of the spot provided.
         * Images are expected to be stored in /images/spots/[icon category name]/[spot classification].png
         *
         * @param spot A spot object
         * @returns array {url: string, size: google.maps.Size, origin: Point, anchor: Point, scaledSize: google.maps.Size}
         */
        function getIconForSpot(spot){

            let baseIconDirectory = "images/spots";
            let iconCategory = spot.type.category.name;
            let iconDirectory = iconCategory.toLowerCase().trim();
            let classification = spot.classification;
            let icon = classification+".png";
            let url = baseIconDirectory+"/"+iconDirectory+"/"+icon;

            if(!icons.hasOwnProperty(iconCategory)) icons[iconCategory] = {};
            if(!icons[iconCategory].hasOwnProperty(url)){

                icons[iconCategory][url] = classification === "review" ? "Under Review" : classification;

            }

            return {
                url: url,
                size: new google.maps.Size(30, 35),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(15, 35),
                scaledSize: new google.maps.Size(30, 35)
            };

        }

        /**
         * Function to build the map legend based on the spots that the site is rendering.
         *
         * @return void.
         */
        function buildLegend(){

            let legend = document.getElementById('legend');
            for (let classificationKey in icons) {

                let classificationIcons = icons[classificationKey];
                let classificationSection = document.createElement('div');
                classificationSection.className = 'legendSection';
                classificationSection.innerHTML = '<h3>'+classificationKey+' Spot</h3>';
                for(let iconUrl in classificationIcons){

                    let legendRow = document.createElement('div');
                    let name = classificationIcons[iconUrl];
                    legendRow.innerHTML = '<img src="' + iconUrl + '"> ' + name;
                    classificationSection.appendChild(legendRow);

                }
                legend.appendChild(classificationSection);

            }

        }

        /**
         * Function to drop a single spot on the map.
         *
         * @return google.maps.Marker
         */
        function dropSpot(spot){

            let marker = new google.maps.Marker({

                position: {lat: Number(spot.lat), lng:Number(spot.lng)},
                animation: google.maps.Animation.DROP,
                map: window.map,
                icon: getIconForSpot(spot),
                title: spot.name,
                draggable: spot.hasOwnProperty('draggable') ? spot.draggable : false

            });

            marker.infoWindow = addClickHandler(marker, spot);

            markers.push(marker);

            return marker;

        }

        /**
         * Function to drop all of the spots the page was given.
         * Calls dropSpot() multiple times.
         *
         * @return void.
         */
        function dropSpots(){

            for(let spotIndex in spots){

                dropSpot(spots[spotIndex]);

            }

        }

        /**
         * Function google calls when the maps API is finally loaded.
         *
         * @return void.
         */
        function initMap() {

            /**
             * Function to restrict the movement of the Google Map so that the user doesn't loose campus.
             *
             * @return void.
             */
            function restrictMapMovement(){

                let lastValidCenter = map.getCenter();
                let allowedBounds = new google.maps.LatLngBounds(

                    new google.maps.LatLng(43.08138,-77.68277),
                    new google.maps.LatLng(43.087664,-77.666849)

                );
                google.maps.event.addListener(map, 'center_changed', function() {

                    if (allowedBounds.contains(map.getCenter())) {

                        lastValidCenter = map.getCenter();
                        return;

                    }
                    map.panTo(lastValidCenter);

                });
                map.setOptions({ minZoom: 15, maxZoom: 20 });

            }

            // Instantiate Google Maps on the page
            window.map = new google.maps.Map(document.getElementById('napMap'), {

                center: <?= json_encode($map["center"]) ?>,
                zoom: 16

            });

            // sortSpots();
            restrictMapMovement();
            // dropSpots();
            // buildLegend();

            @if(session()->has('api_key'))
                console.log('has api key');
                console.log(window.axios.defaults.headers.common['Authorization'] = "bearer <?= session('api_key') ?>");
            @endif

            setTimeout(() => {

                window.builder = new Builder();
                builder.build();

            }, 0);

        }

        /**
         * Function to cancel the creation of a new spot.
         *
         * @return void.
         */
        function cancelNewSpot(){

            window.newSpot.setMap(null);
            window.newSpot = undefined;
            window.markers.splice(window.markers.length - 1, 1);
            $('#new-spot').fadeIn(100);

        }

        /**
         * Function to bind the new spot event handlers.
         *
         * @return void.
         */
        function bindCancelNewSpot(){

            $(document).on("keyup", function(e){

                if(e.keyCode === 8 || e.keyCode === 27){ // Backspace or Escape

                    cancelNewSpot();

                }

            });

            google.maps.event.addListener(window.newSpot.infoWindow, 'closeclick', function(){

                cancelNewSpot();

            });

        }

        /**
         * Function to do all of the steps necessary for the user to be able to create a new spot.
         *
         * @return void.
         */
        function createNewSpot(){

            // Close any open info window.
            if(openInfoWindow)openInfoWindow.close();

            // Drop the new spot.
            window.newSpot = dropSpot({

                draggable:true,
                classification:"preview",
                lat:43.08405904482492,
                lng:-77.67556776826171,
                title:"NEW SPOT",
                type:{
                    category:{
                        name:"Nap",
                        description:"Test"
                    }
                },
                notes: ""

            });

            // Open the new info window after a delay to account for animations.
            setTimeout(function(){

                newSpot.infoWindow.open(newSpot.get('map'), newSpot);
                openInfoWindow = newSpot.infoWindow;

            },400);

            // Bind handlers for canceling the new spot.
            bindCancelNewSpot();

        }

        /**
         * Bind to page load.
         */
        $(document).ready(function(){

            // Register handler for clicking the new-map button
            $('#new-spot').click(function(event){

                // Hide the new-map button.
                $(this).fadeOut(100);
                createNewSpot();

            });

        });

	</script>

	<script src="https://maps.googleapis.com/maps/api/js?key=<?= $map["api_key"] ?>&callback=initMap" async defer></script>

@endsection

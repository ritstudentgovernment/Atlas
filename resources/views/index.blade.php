@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/index.css') }}">

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $map["api_key"] ?>&callback=initMap" async defer></script>

@endsection

@section("body")

    <div id="map">

        <div id="legend"></div>
        <div id="napMap"></div>
        <button id="new-nap" class="cursor circle material-hover transition">
            <span class="material-icon-container"><i class="material-icons">add</i></span>
        </button>

    </div>

@endsection

@section("scripts")

    <script>

        var markers = [],
            icons = {},
            openInfoWindow = false,
            spots = <?= json_encode($spots) ?>;

        function restrictMapMovement(){

            var lastValidCenter = map.getCenter();
            var allowedBounds = new google.maps.LatLngBounds(

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

        function getIconForSpot(spot){

            var baseIconDirectory = "images/spots";
            var iconCategory = spot.type.category.name;
            var icon_directory = iconCategory.toLowerCase().trim();
            var classification = spot.classification;
            var icon = classification+".png";
            var url = baseIconDirectory+"/"+icon_directory+"/"+icon;

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

        function addClickHandler(marker, spot){

            var infowindow = new google.maps.InfoWindow({

                content: spot.type.category.description

            });

            marker.addListener('click', function() {

                if(openInfoWindow)openInfoWindow.close();

                window.map.setZoom(16);
                window.map.panTo(marker.getPosition());

                infowindow.open(marker.get('map'), marker);
                openInfoWindow = infowindow;

            });

        }

        function dropSpots(){

            for(var spotIndex in spots){

                var spot = spots[spotIndex];
                var marker = new google.maps.Marker({

                    position: {lat: Number(spot.lat), lng:Number(spot.lng)},
                    animation: google.maps.Animation.DROP,
                    map: window.map,
                    icon: getIconForSpot(spot),
                    title: spot.name

                });

                addClickHandler(marker, spot);

                markers.push(marker);

            }

        }

        function buildLegend(){

            var legend = document.getElementById('legend');
            for (var classificationKey in icons) {

                var classificationIcons = icons[classificationKey];
                var classificationSection = document.createElement('div');
                classificationSection.className = 'legendSection';
                classificationSection.innerHTML = '<h3>'+classificationKey+' Spot</h3>';
                for(var iconUrl in classificationIcons){

                    var legendRow = document.createElement('div');
                    var name = classificationIcons[iconUrl];
                    legendRow.innerHTML = '<img src="' + iconUrl + '"> ' + name;
                    classificationSection.appendChild(legendRow);

                }
                legend.appendChild(classificationSection);

            }

        }

        function initMap() {

            // Instantiate Google Maps on the page
            window.map = new google.maps.Map(document.getElementById('napMap'), {

                center: <?= json_encode($map["center"]) ?>,
                zoom: 16

            });

            restrictMapMovement();
            dropSpots();
            buildLegend();

        }

    </script>

@endsection
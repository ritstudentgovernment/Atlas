@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/index.css') }}">

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $map["api_key"] ?>&callback=initMap" async defer></script>

@endsection

@section("body")

    <div id="map"></div>

@endsection
<script>

    var markers = [],
        spots = <?= json_encode($spots) ?>,
        icons = {},
        iconBase = "/images/",
        iconPrefixes = <?= json_encode($pin_icon_prefixes) ?>,
        iconTypes = [
            "Designated",
            "Public",
            "Under Review"
        ];

    // Generate icons map from given prefixes
    for(var iconPrefixIndex in iconPrefixes){

        var iconPrefix = iconPrefixes[iconPrefixIndex];
        for(var iconTypeIndex in iconTypes) {

            var iconType = iconTypes[iconTypeIndex];

            var iconIdentifier =
                iconPrefix.toLowerCase().replace(/ /g, '_')
                + "_" +
                iconType.toLowerCase().replace(/ /g, '_');

            icons[iconIdentifier] = {

                name: capitalizeFirstLetter(iconPrefix) + " " + iconType,
                icon: iconBase + iconIdentifier + "_marker.png"

            }

        }

    }

    function capitalizeFirstLetter(string) {

        return string.charAt(0).toUpperCase() + string.slice(1);

    }

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

    }

    function dropSpots(){

        for(var spotIndex in spots){

            var spot = spots[spotIndex];
            markers.push(

                new google.maps.Marker({

                    position: {lat: Number(spot.lat), lng:Number(spot.lng)},
                    animation: google.maps.Animation.DROP,
                    map: window.map

                })

            );

        }

    }

    function initMap() {

        // Instantiate Google Maps on the page
        window.map = new google.maps.Map(document.getElementById('map'), {

            center: <?= json_encode($map["center"]) ?>,
            zoom: 16

        });

        dropSpots();
        restrictMapMovement();

    }

</script>

@section("scripts")
@endsection
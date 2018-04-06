@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/index.css') }}">

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $maps_api_key ?>&callback=initMap" async defer></script>

@endsection

@section("body")

    <div id="map"></div>
    <script>
        function initMap() {
            // Create a map object and specify the DOM element for display.
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 43.08405904482492, lng: -77.67556776826171},
                zoom: 16
            });
        }
    </script>

@endsection
@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/home.css') }}">
    <meta name="googleMapsCenter" content='<?= json_encode($map["center"]); ?>'>
    <meta name="user" content='<?= json_encode($user); ?>'>
    <script src="{{ mix('js/home.js') }}"></script>

@endsection

@section("body")

    <div id="map">

        <div id="legend"></div>
        <div id="napMap"></div>

        @auth
            <new-spot-component></new-spot-component>
        @endauth

    </div>

@endsection

@section("scripts")

    <script src="{{ mix('/js/home.js') }}" async defer></script>

    <script>

        /**
         * Function to do all of the steps necessary for the user to be able to create a new spot.
         *
         * @return void.
         */
        function createNewSpot(){

            // Close any open info window.
            if(openInfoWindow)openInfoWindow.close();

            let spotData = {

            };

            let newspot = new Spot(spotData);
            newspot.drop(true);

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
                // $(this).fadeOut(100);
                // createNewSpot();

            });

        });

	</script>

    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $map["api_key"] ?>&callback=initMap" async></script>

@endsection

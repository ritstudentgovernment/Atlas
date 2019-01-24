@extends("layouts.main")

@section('title', 'Home')

@section('page_head')

    <link rel="stylesheet" href="{{ mix('/css/home.css') }}">
    <meta name="googleMapsCenter" content='<?= json_encode($map["center"]); ?>'>
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

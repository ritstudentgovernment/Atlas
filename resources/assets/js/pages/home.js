import Builder from "../classes/Builder"

window.markers = [];
window.icons = {};
window.openInfoWindow = false;

window.getMeta = (metaName) => {
    let metas = document.getElementsByTagName('meta');
    for (let i = 0; i < metas.length; i++) {
        if (metas[i].getAttribute('name') === metaName) {
            return metas[i].getAttribute('content');
        }
    }
    return '';
};

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

    // Sometimes the map will fail to display, so if the tiles are not loaded after a second try it again.
    setTimeout(() => {

        if (window.map.tilesloading) {

            instantiateMap();
            restrictMapMovement();
            builder.instantiateSpots(window.spotData);

        }

    }, 2000);

};
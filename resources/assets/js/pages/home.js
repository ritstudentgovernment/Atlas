import SpotsAPI from "../classes/api/SpotsAPI"
import Builder from "../classes/Builder"

window.spotsApi = new SpotsAPI(window.api);

window.markers = [];
window.icons = {};
window.openInfoWindow = false;

window.coreApiLoaded = (api) => {

    window.spotsApi.setApi(api);

};

/**
 * Function google calls when the maps API is finally loaded.
 *
 * @return void.
 */
window.initMap = () => {
    function verifyPageLoadedProperly(){
        if (typeof getMeta === 'undefined') {
            window.location.reload();
        }
    }
    /**
     * Function to show the google map at the appropriate location
     *
     * @return Object.
     */
    function instantiateMap(){
        let center = getMeta('googleMapsCenter');
        // Instantiate Google Maps on the page
        window.map = new google.maps.Map(document.getElementById('napMap'), {
            center: center,
            zoom: 16
        });
        return center;
    }
    /**
     * Function to restrict the movement of the Google Map so that the user doesn't loose campus.
     *
     * @return void.
     */
    function restrictMapMovement(center){

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

    verifyPageLoadedProperly();

    let center = instantiateMap();
    restrictMapMovement(center);

    window.builder = new Builder();
    builder.build();

};
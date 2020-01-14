import AdminAPI from "../classes/api/AdminAPI"

window.adminApi = new AdminAPI(window.api);

window.coreApiLoaded = (api) => {

    window.adminApi.setApi(api);

};
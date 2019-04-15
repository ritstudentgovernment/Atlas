import AdminAPI from "../classes/api/AdminAPI"

window.adminApi = new AdminAPI(window.api);

window.coreApiLoaded = (api) => {

    console.log(window.api.axios.defaults.headers.common.Authorization);
    window.adminApi.setApi(api);

};
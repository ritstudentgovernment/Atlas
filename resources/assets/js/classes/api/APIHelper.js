export default class APIHelper {

    constructor(api = false) {
        this.prefix = "";
        this.api = api;
    }

    setApi(api) {
        this.api = api;
    }

    get (url, parameters = {}) {
        return this.api.get(`${this.prefix}/${url}`, parameters);
    }

    post (url, parameters = {}) {
        return this.api.post(`${this.prefix}/${url}`, parameters);
    }

}

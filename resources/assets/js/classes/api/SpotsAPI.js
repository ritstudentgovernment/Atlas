import APIHelper from "./APIHelper";

export default class SpotsAPI extends APIHelper {

    constructor (api) {
        super(api);
        this.prefix = "spots";
    }

    categories () {
        return this.get('categories');
    }

    list () {
        return this.get('');
    }

    approve(spotId) {
        return this.post(`approve/${spotId}`);
    }

}
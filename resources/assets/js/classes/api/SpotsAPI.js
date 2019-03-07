import API from './API'

export default class SpotsAPI{

    constructor(api = false) {
        this.api = api;
        if (!(api instanceof API)) {
            this.api = new API(window.axios);
        }
    }

    get(url, parameters = {}){
        return this.api.get(`spots/${url}`, parameters);
    }

    post(url, parameters = {}){
        return this.api.post(`spots/${url}`, parameters);
    }

    categories(){
        return this.get('categories');
    }

    list(){
        return this.get('');
    }

    approve(spotId) {
        return this.post(`approve/${spotId}`);
    }

}
import API from './API'

export default class SpotsAPI{

    constructor(api = false) {
        this.api = api;
        if (!(api instanceof API)) {
            this.api = new API(window.axios);
        }
    }


    list(){
        return this.api.get('spots');
    }

    approve(spotId){
        return this.api.post(`spots/approve/${spotId}`);
    }

    get(url, parameters = {}){
        return this.api.get(`spots/${url}`, parameters);
    }

    post(url, parameters = {}){
        return this.api.post(`spots/${url}`, parameters);
    }

}
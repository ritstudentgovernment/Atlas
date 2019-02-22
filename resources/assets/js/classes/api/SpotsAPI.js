import API from './API'

export default class SpotsAPI{

    constructor(api = false){
        if (api instanceof API) {
            this.api = api;
        } else {
            console.error('must send api instance')
        }
    }

    list(){
        return this.api.get('spots');
    }

    approve(spotId){
        return this.api.post(`spots/approve/${spotId}`);
    }

}
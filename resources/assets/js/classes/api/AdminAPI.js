import APIHelper from './APIHelper'

export default class AdminAPI extends APIHelper{

    constructor (api) {
        super(api);
        this.prefix = "admin";
    }

}
import APIHelper from './APIHelper'

export default class AdminAPI extends APIHelper{

    constructor (api) {
        super(api);
        this.prefix = "admin";
    }

    post (url, parameters = {}) {
        return super.post(url, parameters).catch((error) => {
            window.vue.$notify.error({
                title: 'Error',
                message: error.response.data.message
            });
        });
    }

}
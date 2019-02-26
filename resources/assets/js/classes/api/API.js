export default class API {

    constructor(axios) {
        this.axios = axios;
        this.errors = [];
    }

    request(axiosRequest) {
        let self = this;
        return new Promise((resolve, reject) => {
            axiosRequest.then((response) => resolve(response)).catch((error) => {
                self.errors.push(error);
                console.error(error);
                reject(error);
            });
        });
    }

    get(url, parameters = {}) {
        return this.request(this.axios.get(`api/${url}`, parameters));
    }

    post(url, parameters = {}) {
        return this.request(this.axios.post(`api/${url}`, parameters));
    }

}

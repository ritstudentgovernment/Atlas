export default class API {

    constructor(axios) {
        this.axios = axios;
        this.errors = [];
    }

    get(url, parameters = {}) {
        let self = this;
        return new Promise((resolve, reject) => {
            self.axios.get(`api/${url}`, parameters).then((response) => resolve(response)).catch((error) => {
                self.errors.push(error);
                console.error(error);
                reject(error);
            });
        });
    }

    post(url, parameters = {}) {
        let self = this;
        return new Promise((resolve, reject) => {
            self.axios.post(`api/${url}`, parameters).then((response) => resolve(response)).catch((error) => {
                self.errors.push(error);
                console.error(error);
                reject(error);
            });
        });
    }

}

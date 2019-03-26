export default class API {

    constructor (axios) {
        this.url = '';
        this.errors = [];
        this.method = '';
        this.axios = axios;
        this.parameters = {};
    }

    handleError (error) {
        this.errors.push(error);
        if (error.response.status === 401) {
            window.location.href = "/login";
        }
    }

    makeRequest () {
        let self = this;
        return new Promise((resolve, reject) => {
            self.axios[self.method](self.url, self.parameters)
                .then((response) => resolve(response))
                .catch((error) => {
                    this.handleError(error);
                    reject(error);
                });
            });
    }

    setState (url, parameters, method) {
        this.url = `api/${url}`;
        this.method = method;
        this.parameters = parameters;
    }

    get (url, parameters = {}) {
        this.setState(url, parameters, 'get');
        return this.makeRequest();
    }

    post (url, parameters = {}) {
        this.setState(url, parameters, 'post');
        return this.makeRequest();
    }

}


/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.prototype.$http = axios;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

const app = new Vue({
    el: '#app'
});

$.urlParam = function(name, value = false){

    let url = window.location.href;

    // Check if we are just looking to get the value of a url parameter
    if (!value) {
        // We are, do some regex magic and get the value.
        let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
        if (results){
            return decodeURI(results[1]) || 0;
        }
        return null;
    }

    // We are trying to update the value of a url parameter (or set one)
    let newUrl = "";
    let splitUrl = [];
    let currentValue = $.urlParam(name);

    // just return true if the current value matches the value we are tyring to set it to.
    if (value === currentValue) { return true; }

    // the value is different than what you want it to be, or not set at all, lets fix that.
    if (currentValue) {
        // The URL Parameter exists currently, we have to update it.
        let regex = new RegExp("([?;&])" + name + "[^&;]*[;&]?");
        let query = url.replace(regex, "$1").replace(/&$/, '');
        splitUrl = url.split('&');
        newUrl = query + (splitUrl[1] ? '&' : '') + (value ? name + "=" + value : '');
    } else {
        // The URL Parameter did not exist so we have to create it
        splitUrl = url.split('?');
        newUrl = url + (splitUrl[1] ? '&' : '?') + name + '=' + value;
    }
    window.history.pushState({}, '', newUrl);
    return true;

};
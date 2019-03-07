
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import API from "./classes/api/API"
import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

require('./bootstrap');

window.api = new API(window.axios);

Vue.prototype.$http = window.axios;

Vue.use(ElementUI);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('new-spot', require('./components/pages/home/NewSpot.vue').default);
Vue.component('filter-spots', require('./components/pages/home/FilterSpots.vue').default);
Vue.component('admin-nav', require('./components/pages/admin/Nav.vue').default);

export const EventBus = new Vue();

window.vue = new Vue({
    el: '#app',
    components: {
        ElementUI
    }
});

/**
 * In order to deal with axios headers not being initialized properly (auth and csrf headers missing)
 * early on in the execution trace of the app, specifically in the mounted or created methods of Vue
 * components, use this queue to defer processing until the app.js script is finished loading.
 * Each component should (if calling the API, and does so in its created method) implement API calls
 * in a 'setup' method, and add that method to the onLoadedQueue for deferred processing.
 */
if (window.onLoadedQueue) {
    if (window.spotsApi) {
        let axiosHeaders = window.spotsApi.api.axios.defaults.headers;
        if (!axiosHeaders.common.hasOwnProperty('Authorization')) {
            window.spotsApi.api = window.api;
        }
    }
    window.onLoadedQueue.forEach(methodToCall => {
       methodToCall();
    });
}

const capitalize = (s) => {
    if (typeof s !== 'string') return '';
    return s.charAt(0).toUpperCase() + s.slice(1);
};

$.urlParam = (name, value = false) => {

    function getValue(name, url) {
        // Do some regex magic to get the value.
        let results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
        if (results){
            return decodeURI(results[1]) || 0;
        }
        return null;
    }

    let url = window.location.href,
        currentValue = getValue(name, url);

    // Check if we are just looking to get the value of a url parameter
    if (!value) { return currentValue; }

    // We are trying to update the value of a url parameter (or set one)
    let newUrl = "",
        splitUrl = [];

    // just return true if the current value matches the value we are tyring to set it to.
    if (value === currentValue) { return true; }

    // the value is different than what you want it to be, or not set at all, lets fix that.
    if (currentValue) {
        // The URL Parameter exists currently, we have to update it.
        let regex = new RegExp("([?;&])" + name + "[^&;]*[;&]?"),
            query = url.replace(regex, "$1").replace(/&$/, '');

        splitUrl = url.split('&');
        newUrl = query + (splitUrl[1] ? '&' : '') + (value ? name + "=" + value : '');
    } else {
        // The URL Parameter did not exist so we have to create it
        splitUrl = url.split('?');
        newUrl = url + (splitUrl[1] ? '&' : '?') + name + '=' + value;
    }

    // Set the current url to the updated one, return the value of the parameter
    window.history.pushState({}, '', newUrl);
    return getValue(name);

};
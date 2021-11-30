window._ = require('lodash');

require('../css/app.scss');

require('../css/dynamicSelectCommon.css');

require('../css/tooltip.css');

import "vue-select/src/scss/vue-select.scss";

window.Vue = require('vue').default;

window.eventHub = new Vue();

import {lang} from 'helpers/extraLogics';

import {store} from 'store'

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {

    window.$ = window.jQuery = require('jquery');

} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = document.head.querySelector('meta[name="api-base-url"]').content;
window.axios.defaults.headers.common['Authorization'] = 'Bearer'+' '+ store.getters.getUserToken;
//fetching language file from server and declaring that as global prop
//if file doesn't have the passed key, it is going to return string
Vue.prototype.lang = lang;

// gives basePath
Vue.prototype.basePath = () => (window.axios.defaults.baseURL)

Vue.mixin({
    
    methods: {
    
      basePath : () => (window.axios.defaults.baseURL),

      trans: (string) => lang(string)
    },

    data: () =>({})
});


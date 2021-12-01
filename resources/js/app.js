require('./bootstrap');

import {store} from 'store'

window.Vue = require('vue').default;

Vue.component('license-manager-renderer', require('./components/LicenseManagerRenderer.vue').default);

import router from './router/router';

const app = new Vue({

    el: '#app-license',

    store,

    router
});

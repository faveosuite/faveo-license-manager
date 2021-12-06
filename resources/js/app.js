require('./bootstrap');

import {store} from 'store'

window.Vue = require('vue').default;

import {ServerTable, ClientTable, Event} from 'vue-tables-2';

Vue.use(ClientTable);

Vue.component('license-manager-renderer', require('./components/LicenseManagerRenderer.vue').default);

Vue.component('alert', require('./components/Reusable/Alert.vue').default);

Vue.component('loader', require('./components/Reusable/Loader.vue').default);

Vue.component('custom-loader', require('./components/Reusable/CustomLoader.vue').default);

Vue.component('data-table', require('./components/Reusable/Datatable.vue').default);

Vue.component('tool-tip', require('./components/Reusable/Tooltip.vue').default);

Vue.component('modal', require('./components/Reusable/Modal.vue').default);

import router from './router/router';

const app = new Vue({

    el: '#app-license',

    store,

    router
});

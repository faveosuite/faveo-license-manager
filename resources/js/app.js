// import './bootstrap';
//
// import {store} from 'store'
//
// window.Vue = require('vue').default;
//
// import {ServerTable, ClientTable, Event} from 'vue-tables-2';
//
// Vue.use(ClientTable);
//
// Vue.component('license-manager-renderer', require('./components/LicenseManagerRenderer.vue').default);
//
// Vue.component('alert', require('./components/Reusable/Alert.vue').default);
//
// Vue.component('loader', require('./components/Reusable/Loader.vue').default);
//
// Vue.component('custom-loader', require('./components/Reusable/CustomLoader.vue').default);
//
// Vue.component('data-table', require('./components/Reusable/Datatable.vue').default);
//
// Vue.component('table-actions', require('./components/Reusable/DatatableActions.vue').default);
//
// Vue.component('tool-tip', require('./components/Reusable/Tooltip.vue').default);
//
// Vue.component('modal', require('./components/Reusable/Modal.vue').default);
//
// import index from './index/index';
//
// const app = new Vue({
//
//     el: '#app-license',
//
//     store,
//
//     index
// });

import './bootstrap';

import { createApp, h } from 'vue';

import router from './router';

import store from './store';

import LicenseManagerRenderer from "./Layouts/LicenseManagerRenderer.vue";

let app = createApp({});

app.component('license-manager-renderer', LicenseManagerRenderer);

import Tooltip from "./components/Reusable/Tooltip.vue";

import VTooltip from "v-tooltip";

app.use(VTooltip);

import "v-tooltip/dist/v-tooltip.css";

app.component('tool-tip', Tooltip);

import {ServerTable, ClientTable, EventBus} from 'v-tables-3';

app.use(ClientTable)
app.use(ServerTable)


import mitt from 'mitt';
const emitter = mitt();
app.config.globalProperties.emitter = emitter;
window.emitter = emitter;
emitter.on('*', console.info.bind(console, 'event: '));


import Alert from "./components/Reusable/Alert.vue";
import Loader from "./components/Reusable/Loader.vue";
import CustomLoader from "./components/Reusable/CustomLoader.vue";
import DatatableActions from "./components/Reusable/DatatableActions.vue";
import Modal from './components/Reusable/Modal.vue';

app.component('alert', Alert);
app.component('loader', Loader);
app.component('custom-loader', CustomLoader);
app.component('table-actions', DatatableActions);
app.component('modal', Modal);

import globalMixins from './globalMixins.js'

app.mixin(globalMixins)

app.use(router)

app.use(store)

app.mount('#app');

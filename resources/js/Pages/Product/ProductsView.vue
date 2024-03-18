<template>

    <div class="container-fluid">

        <div class="container col-md-12">

            <div class="card card-header-tabs card-outline">

                <div class="card-header card-header-dark card-light border-bottom">

                    <h3 class="card-title">{{product_title}}</h3>

                    <div class="card-tools">

                        <router-link :to="'/products/'+ id +'/edit'" class="btn btn-tool action-btn">

                            <i class="fas fa-edit"></i>
                        </router-link>

                        <button class="btn btn-tool action-btn" @click="showDeleteModal()">

                            <i class="fas fa-trash"></i>
                        </button>

                    </div>
                </div>

                <div class="row card-body col-md-12">

                    <div class="row pt-2 pb-2 d-flex align-content-between border-bottom col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('product_name')}}:</label>
                        <div v-if="product_title" class="col-sm-6 text-sm">{{product_title}}</div>
                        <span class="col-sm-6" v-else >----</span>
                    </div>

                    <div class="row pt-2 pb-2 border-bottom col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('sku_id')}}:</label>
                        <div v-if="product_sku" class="col-sm-6 text-sm">{{product_sku}}</div>
                        <span class="col-sm-6" v-else >----</span>
                    </div>

                    <div class="row pt-2 pb-2 border-bottom col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('homepage_url')}}</label>
                        <div v-if="product_url_homepage" class="col-sm-6 text-sm"><a :href="product_url_homepage" target="_blank">{{product_url_homepage}}</a></div>
                        <span class="col-sm-6" v-else >----</span>
                    </div>

                    <div class="row pt-2 pb-2 border-bottom col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('product_download_url')}}:</label>
                        <div v-if="product_url_download" class="col-sm-6 text-sm"><a :href="product_url_download" target="_blank">{{product_url_download}}</a></div>
                        <span class="col-sm-6" v-else >----</span>
                    </div>

                    <div class="row pt-2 pb-2 col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('product_latest_version')}}:</label>
                        <router-link :to="'/versions/'+version_id+'/view'" v-if="version_number" class="col-sm-6 text-sm">{{version_number}}</router-link>
                        <span class="col-sm-6" v-else >----</span>
                    </div>

                    <div class="row pt-2 pb-2 col-sm-6">
                        <label class="col-sm-6 text-sm font-weight-bold">{{lang('product_status')}}:</label>
                        <div v-if="product_status" class="col-sm-6 text-sm text-success">{{lang('active')}}</div>
                        <div v-else class="col-sm-6 text-sm text-danger">{{lang('inactive')}}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container col-sm-12">

            <div class="row" v-if="loading">

                <custom-loader :duration="4000"></custom-loader>
            </div>

            <alert componentName="product" />

            <div class="card card-header-tabs">

                <div class="card-header data-table-header border-0 p-0 pt-1">

                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">

                        <li class="nav-item">

                            <span class="nav-link card-header-link active" @click="updateData('installations')" id="custom-tabs-one-home-tab" data-toggle="pill" role="tab" aria-controls="custom-tabs-one-home">{{lang('installations')}}</span>
                        </li>

                        <li class="nav-item">

                            <span class="nav-link card-header-link" @click="updateData('licenses')" id="custom-tabs-one-profile-tab" data-toggle="pill" role="tab">{{lang('licenses')}}</span>
                        </li>

                        <li class="nav-item">

                            <span class="nav-link card-header-link" @click="updateData('versions')" id="custom-tabs-one-profile-tab" data-toggle="pill" role="tab">{{lang('versions')}}</span>
                        </li>
                    </ul>
                </div>

                <div class="card-body">

                    <data-table v-if="!loading" :url="endPoint" ref="dataTable" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="licenses-list">

                    </data-table>
                </div>

            </div>

        </div>

        <transition name="modal">

            <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" deleteUrl="/api/admin/products/delete" keyVal="product_id" :idVal="id">

            </delete-modal>
        </transition>

    </div>

</template>

<script>
import {boolean, getIdFromUrl, lang} from "../../helpers/extraLogics";
import moment from "moment";
import 'moment-timezone'
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import axios from "axios";
import DeleteModal from "../../components/Reusable/DeleteModal.vue";
import {h} from "vue";
import {RouterLink} from "vue-router";

export default {
    name: "product-view",

    data() {

        return {

            endPoint : '',

            showModal : false,

            hasDataPopulated: false,

            product_title: '',

            product_sku: '',

            product_url_homepage: '',

            product_url_download: '',

            product_status: '',

            version_number: '',

            version_id: '',

            columns: [],

            options: {},

            loading: false,

            id: null
        }
    },

    components: {
        'delete-modal': DeleteModal,

        'data-table': DynamicDataTable
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);
    },

    props : {
        generalSetting : {type : Object, default : () => {}},
    },

    methods: {
        lang,

        showDeleteModal(){

            this.showModal = !this.showModal;
        },

        onClose(){

            this.showModal = false;

            this.$store.dispatch('unsetValidationError');
        },

        getValues(path) {

            const productId = getIdFromUrl(path)

            this.hasDataPopulated = false

            this.getInitialValues(productId);

            this.updateData('installations', productId)
        },

        getInitialValues(id) {

            this.loading = true

            axios.get('/api/admin/productView/' + id).then(res => {

                this.loading = false;

                this.hasDataPopulated = true

                this.updateStatesWithData(res.data.data);

            }).catch(error => {

                this.loading = false;
            });
        },

        updateStatesWithData(data) {

            const self = this;

            const stateData = this.$data;

            Object.keys(data).map(key => {

                if (stateData.hasOwnProperty(key)) {

                    self[key] = data[key];
                }
            });

            this.version_number = data.versions[0]?.version_number
            this.version_id = data.versions[0]?.version_id
        },

        updateData(value, productId) {

            const date_format = this.generalSetting.date_format.js_format
            const time_format = this.generalSetting.time_format.js_format
            const timezone = this.generalSetting.timezone.name

            this.id = productId ? productId : this.id

            if(value === 'installations') {

                this.loading = true

                this.endPoint = '/api/admin/productInstallations/' + this.id

                this.columns = ['installation_domain', 'ip_address', 'client_email', 'license', 'installation_date', 'installation_status', 'actions']

                this.options = {

                        sortIcon: {

                            base: 'glyphicon',

                            up: 'glyphicon-chevron-up',

                            down: 'glyphicon-chevron-down'
                        },

                        texts: { filter: '', limit: '' },

                        sortable:  ['client_email', 'installation_date', 'installation_status'],

                        filterable : [ 'client_email' ],

                        requestAdapter(data) {

                            return {

                                'sort_field' : data.orderBy ? data.orderBy : 'installation_id',

                                'sort_order' : data.ascending ? 'desc' : 'asc',

                                'search_query' : data.query.trim(),

                                perPage : data.limit,
                            }
                        },

                        responseAdapter({data}) {

                            return {

                                data: data.data.data.map(data => {

                                    data.edit_url = '/installations/' + data.installation_id + '/edit';

                                    data.delete_url = '/api/admin/installations/delete';

                                    data.view_url = '/installations/' + data.installation_id + '/view';

                                    data.keyVal = 'installation_id';

                                    data.idVal = data.installation_id;

                                    return data;
                                }),
                                count: data.data.total
                            }
                        },

                        columnsClasses: {

                            license: 'license_code',

                            client_email: 'client_email',

                            installation_domain: 'installation_domain',

                            ip_address: 'installation_ip',

                            installation_date: 'installation_date',

                            installation_status: 'installation_status',

                            status: 'status',
                        },

                        templates: {

                            ip_address(h, row) {

                                return row.installation_ip ? row.installation_ip : '----'
                            },

                            installation_date(h, row) {

                                return row.installation_date ? moment(row.installation_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            client_email: (f, row) => {

                                if(row.client_email && row.client_id) {

                                    return h(RouterLink, {

                                        to: '/clients/' + row.client_id + '/view'

                                    },[row.client_email])

                                } else {
                                    return '----'
                                }
                            },

                            license: (f, row) => {

                                if(row.license && row.license.license_code && row.license.license_id) {

                                    return h(RouterLink, {

                                        to: '/licenses/' + row.license.license_id + '/view'

                                    },[row.license.license_code.match(/.{1,4}/g).join('-')])

                                } else {
                                    return '----'
                                }
                            },

                            installation_status: (f, row) => {

                                return h('span', {
                                    'class': row.installation_status ? 'text-green' : 'text-red'
                                }, row.installation_status ? this.lang('active'): this.lang('inactive'))
                            },

                            installation_domain: (f, row) => {

                                if(row.installation_domain) {

                                    return h('a', {

                                        href: row.installation_domain,
                                        target: '_blank'

                                    },[row.installation_domain])

                                } else {
                                    return '----'
                                }
                            },
                        },

                        pagination: { show : false },

                        headings: {

                            license: this.lang('license_code'),

                            client_email: this.lang('email'),

                            installation_domain: this.lang('domain'),

                            ip_address: this.lang('ip'),

                            installation_date: this.lang('installation_date'),

                            installation_status: this.lang('status'),

                            actions: this.lang('actions')
                        },
                    }

                this.loading = false

            } else if(value === 'licenses') {

                this.loading = true

                this.endPoint = '/api/admin/productLicenses/' + this.id

                this.columns = ['license_code','client_email', 'license_date', 'license_expire_date', 'license_updates_date', 'license_support_date',
                    'installation_counts', 'latest_call_backs', 'license_status', 'actions']

                this.options = {

                        sortIcon: {

                            base: 'glyphicon',

                            up: 'glyphicon-chevron-up',

                            down: 'glyphicon-chevron-down'
                        },

                        texts: { filter: '', limit: '' },

                        sortable:  ['client_email', 'license_code','license_date', 'license_expire_date', 'license_updates_date', 'license_support_date', 'latest_license_date', 'license_status'],

                        filterable:  ['client_email'],

                        requestAdapter(data) {

                            return {

                                'sort_field' : data.orderBy ? data.orderBy : 'license_id',

                                'sort_order' : data.ascending ? 'desc' : 'asc',

                                'search_query' : data.query.trim(),

                                perPage : data.limit,
                            }
                        },

                        columnsClasses: {

                            client_email: 'client_email',

                            license_code: 'license_code',

                            license_date: 'license_date',

                            license_expire_date : 'license_expire_date',

                            license_updates_date: 'license_updates_date',

                            license_support_date: 'license_support_date',

                            installation_counts: 'installations_counts',

                            latest_license_date: 'latest_license',

                            latest_call_backs: 'latest_call_backs',

                            actions:      'actions',
                        },

                        templates: {

                            license_date(h, row) {

                                return row.license_date ? moment(row.license_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            license_expire_date(h, row) {

                                return row.license_expire_date ? moment(row.license_expire_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            license_updates_date(h, row) {

                                return row.license_updates_date ? moment(row.license_updates_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            license_support_date(h, row) {

                                return row.license_support_date ? moment(row.license_support_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            latest_call_backs(h, row) {

                                return row.latest_call_backs ? moment(row.latest_call_backs).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                            },

                            license_code: (f, row) => {

                                if(row.license_code && row.license_id) {

                                    return h(RouterLink, {

                                        to: '/licenses/' + row.license_id + '/view'

                                    },[row.license_code.match(/.{1,4}/g).join('-')])

                                } else {
                                    return '----'
                                }
                            },

                            license_status: (f, row) => {

                                return h('span', {
                                    'class': row.license_status ? 'text-green' : 'text-red'
                                }, row.license_status ? this.lang('active'): this.lang('inactive'))
                            },

                            client_email: (f, row) => {

                                if(row.client_email) {

                                    return h(RouterLink, {

                                        to: '/clients/' + row.client_id + '/view'

                                    },[row.client_email])

                                } else {
                                    return '----'
                                }
                            },

                        },

                        pagination: { show : false },

                        headings: {

                            license_code: this.lang('license_code'),

                            client_email: this.lang('email'),

                            license_date: this.lang('activation_date'),

                            license_expire_date : this.lang('expiration_date'),

                            license_updates_date: this.lang('updates_expiry'),

                            license_support_date: this.lang('support_expiry'),

                            installation_counts: this.lang('no_of_installations'),

                            latest_license_date: this.lang('latest_license'),

                            latest_call_backs: this.lang('latest_callbacks'),

                            actions: this.lang('actions')
                        },

                        responseAdapter({data}) {

                            return {

                                data: data.data.data.map(data => {

                                    data.edit_url = '/licenses/' + data.license_id + '/edit';

                                    data.delete_url = '/api/admin/license/delete';

                                    data.view_url = '/licenses/' + data.license_id + '/view'

                                    data.keyVal = 'license_id';

                                    data.idVal = data.license_id;

                                    return data;
                                }),
                                count: data.data.total
                            }
                        },
                    }

                this.loading = false

            } else {

                this.loading = true

                this.endPoint = '/api/admin/productVersions/' + this.id

                this.columns = ['version_number', 'version_date', 'version_upgrade_count', 'version_status', 'actions']

                this.options = {

                    sortIcon: {

                        base: 'glyphicon',

                        up: 'glyphicon-chevron-up',

                        down: 'glyphicon-chevron-down'
                    },

                    texts: { filter: '', limit: '' },

                    sortable:  ['version_number','version_date', 'version_upgrade_count', 'version_status'],

                    filterable:  ['version_number'],

                    requestAdapter(data) {

                        return {

                            'sort_field' : data.orderBy ? data.orderBy : 'version_id',

                            'sort_order' : data.ascending ? 'desc' : 'asc',

                            'search_query' : data.query.trim(),

                             perPage : data.limit,
                        }
                    },

                    columnsClasses: {

                        version_number: 'version_number',

                        version_date: 'version_date',

                        version_upgrade_count : 'version_upgrade_count',

                        version_status: 'version_status',

                        actions:      'actions',
                    },

                    templates: {

                        version_date(h, row) {

                            return row.version_date ? moment(row.version_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                        },

                        version_status: (f, row) => {

                            return h('span', {
                                'class': row.version_status ? 'text-green' : 'text-red'
                            }, row.version_status ? this.lang('active'): this.lang('inactive'))
                        },

                        version_number: (f, row) => {

                            if(row.version_number && row.version_id) {

                                return h(RouterLink, {

                                    to: '/versions/' + row.version_id + '/view'

                                },[row.version_number])

                            } else {
                                return '----'
                            }
                        },

                    },

                    pagination: { show : false },

                    headings: {

                        version_number: this.lang('version'),

                        version_date: this.lang('release_date'),

                        version_upgrade_count : this.lang('upgrades'),

                        version_status: this.lang('status'),

                        actions: this.lang('actions')
                    },

                    responseAdapter({data}) {

                        return {

                            data: data.data.data.map(data => {

                                data.edit_url = '/licenses/' + data.license_id + '/edit';

                                data.delete_url = '/api/admin/license/delete';

                                data.view_url = '/versions/' + data.version_id + '/view';

                                data.keyVal = 'version_id';

                                data.idVal = data.version_id;

                                return data;
                            }),
                            count: data.data.total
                        }
                    },
                }

                this.loading = false

            }
        }

    },
}
</script>

<style scoped>

.card-header-dark {
    background-color: #f8f9fa;
}
.data-table-header {
    background-color: #ebebeb;
}
.action-btn{
    color: rgba(31, 45, 61, .8);
}
.action-btn:hover{
    color: black;
}
.card-header-link{
    color: black;
}
.card-header-link:hover:not(.active){
    color: #007bff;
    cursor: pointer;
}

</style>

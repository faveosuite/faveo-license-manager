<template>
    <div class="container-fluid">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="client-view" />

        <div class="col-md-12">

            <div class="card card-header-tabs card-outline">

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-3 px-5 text-center">

                            <image-element class="object-fit-cover" :class="['profile-user-img', 'img-responsive', 'img-circle', 'img-click']" alt="User Profile Picture" id="client_profile_pic" :sourceUrl="client_profile_pic" ></image-element>

                            <h3 class="profile-username">{{full_name}}</h3>

                            <p class="text-muted">
                                {{client_email}}

                                <span class="btn ml-1 btn-default" v-tooltip="lang('copy')" style="cursor: pointer" @click="copyCommand('email')">
                                    <i :class="iconClassEmail"></i>

                                </span>
                            </p>

                            <div class="client-status-btn">

                                <span v-if="client_status" class="px-4 py-1 user-select-none bg-success">Active</span>
                                <span v-else class="px-4 py-1 user-select-none bg-danger w-50">Inactive</span>

                            </div>

                        </div>

                        <div class="col-md-7 border-left border-2 border-gray px-5">

                            <div class="row mt-3 pb-3">

                                <div class="col-md-12 border-bottom mt-2">
                                    <div class="row">
                                        <div class="col-md-5 text-gray">
                                            <label><strong>{{lang('role')}}:</strong></label>
                                        </div>
                                        <div v-if="client_role" class="col-md-7 client_role">{{client_role}}</div>
                                        <div v-else>----</div>
                                    </div>
                                </div>

                                <div class="col-md-12 border-bottom mt-2">
                                    <div class="row">
                                        <div class="col-md-5 text-gray">
                                            <label><strong>{{lang('user_activation_date')}}:</strong></label>
                                        </div>
                                        <div v-if="client_active_date" class="col-md-7">{{client_active_date}}</div>
                                        <div v-else>----</div>
                                    </div>
                                </div>

                                <div class="col-md-12 border-bottom mt-2">
                                    <div class="row">
                                        <div class="col-md-5 text-gray">
                                            <label><strong>{{lang('organization')}}:</strong></label>
                                        </div>
                                        <div v-if="client_organization" class="col-md-7">{{client_organization}}</div>
                                        <div v-else>----</div>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-2">
                                    <div class="row">
                                        <div class="col-md-5 text-gray">
                                           <label><strong>{{lang('address')}}:</strong></label>
                                        </div>
                                        <div v-if="client_address" class="col-md-7">{{client_address}}</div>
                                        <div v-else>----</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-tools col-md-2">

                            <router-link :to="'/clients/'+ id +'/edit'" v-tooltip="lang('edit')" class="btn mr-2 action-btn text-right">

                                <i class="fas fa-edit"></i>
                            </router-link>

                            <button class="btn action-btn delete-btn p-0" v-tooltip="lang('delete_btn')" @click="showDeleteModal()">

                                <i class="fas fa-trash"></i>
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-sm-12">

<<<<<<< HEAD
=======
            <div class="row" v-if="loading">

                <custom-loader :duration="4000"></custom-loader>
            </div>

            <alert componentName="dataTableModal" />

>>>>>>> a55f642b (refactor fields)
            <div class="card card-header-tabs">

                <div class="card-header border-0 data-table-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link cursor-pointer card-header-link active" @click="updateData('installations')" id="custom-tabs-one-home-tab" data-toggle="pill" href="#" role="tab" aria-controls="custom-tabs-one-home">{{lang('installations')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link card-header-link cursor-pointer" @click="updateData('licenses')" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#" role="tab">{{lang('licenses')}}</a>
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

            <delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" alertComponentName="client-view" deleteUrl="/api/admin/clients/delete" redirectUrl="/clients/list" keyVal="client_id" :idVal="id">

            </delete-modal>
        </transition>

    </div>

</template>

<script>

import {formatDateTime, getIdFromUrl, lang} from "../../helpers/extraLogics";
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import DeleteModal from "../../components/Reusable/DeleteModal.vue";
import {h} from "vue";
import {RouterLink} from "vue-router";
import ImageElement from "../../components/Reusable/ImageElement.vue";
import axios from "axios";
import copy from 'clipboard-copy'
export default {
    name: "client",

    data() {

        return {

            endPoint : '',

            columns: [],

            options: {},

            id: null,

            loading: false,

            showModal: false,

            hasDataPopulated: false,

            iconClassEmail: 'fas fa-copy',

            full_name: '',

            client_email: '',

            client_role: '',

            client_status: null,

            client_active_date: '',

            client_address: '',

            client_organization: '',

            client_profile_pic: ''
        }
    },

    components: {
        'image-element': ImageElement,
        'data-table': DynamicDataTable,
        'delete-modal': DeleteModal
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

        copyCommand() {

            copy(this.client_email)

            this.iconClassEmail = 'fas fa-check'

            setTimeout(()=>{

                this.iconClassEmail = 'fas fa-copy'
            },2000)
        },

        getValues(path) {

            const clientId = getIdFromUrl(path)

            this.hasDataPopulated = false

            this.getInitialValues(clientId);

            this.updateData('installations', clientId)
        },

        getInitialValues(id) {

            this.loading = true

            axios.get('/api/admin/clientView/' + id).then(res => {

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

            if(data.client_active_date) {

                this.client_active_date = formatDateTime(data.client_active_date, this.generalSetting.timezone.name, this.generalSetting.date_format.js_format, this.generalSetting.time_format.js_format)
            }
        },

        updateData(value, productId) {

            const date_format = this.generalSetting.date_format.js_format
            const time_format = this.generalSetting.time_format.js_format
            const timezone = this.generalSetting.timezone.name

            this.id = productId ? productId : this.id  // Because we are only sending id on mounting

            if(value === 'installations') {

                this.loading = true

                this.endPoint = '/api/admin/clientInstallations/' + this.id

                this.columns = ['installation_domain', 'installation_date', 'installation_ip', 'installation_status', 'actions']

                this.options = {

                        sortIcon: {

                            base: 'glyphicon',

                            up: 'glyphicon-chevron-up',

                            down: 'glyphicon-chevron-down'
                        },

                        texts: { filter: '', limit: '' },

                        sortable:  ['installation_date', 'installation_status'],

                        filterable : [ 'installation_domain' ],

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

                                    data.view_url = '/installations/' + data.installation_id + '/view'

                                    data.keyVal = 'installation_id';

                                    data.idVal = data.installation_id;

                                    return data;
                                }),
                                count: data.data.total
                            }
                        },

                        columnsClasses: {

                            installation_domain: 'installation_domain',

                            installation_ip: 'installation_ip',

                            installation_date: 'installation_date',

                            installation_status: 'installation_status',

                            status: 'status',
                        },

                        templates: {

                            installation_ip(h, row) {

                                return row.installation_ip ? row.installation_ip : '----'
                            },

                            installation_date(h, row) {

                                return formatDateTime(row.installation_date, timezone, date_format, time_format)
                            },

                            installation_domain: (f, row) => {

                                if(row.installation_domain) {

                                    return h('a', {

                                        href: 'https://'+row.installation_domain,
                                        target: '_blank'

                                    },[row.installation_domain])

                                } else {
                                    return '----'
                                }
                            },

                            installation_status: (f, row) => {

                                return h('span', {
                                    'class': row.installation_status ? 'text-green' : 'text-red'
                                }, row.installation_status ? this.lang('active'): this.lang('inactive'))
                            },
                        },

                        pagination: { show : false },

                        headings: {

                            installation_domain: this.lang('domain'),

                            installation_ip: this.lang('ip_address'),

                            installation_date: this.lang('installation_date'),

                            installation_status: this.lang('status'),

                            actions: this.lang('actions')
                        },
                    }

                this.loading = false

            } else {

                this.loading = true

                this.endPoint = '/api/admin/clientLicenses/' + this.id

                this.columns = ['product_title' ,'license_date', 'license_expire_date', 'license_updates_date', 'license_support_date',
                    'installation_counts', 'latest_call_backs', 'license_status', 'actions']

                this.options = {

                        sortIcon: {

                            base: 'glyphicon',

                            up: 'glyphicon-chevron-up',

                            down: 'glyphicon-chevron-down'
                        },

                        texts: { filter: '', limit: '' },

                        sortable:  ['license_date', 'license_expire_date', 'license_updates_date', 'license_support_date', 'license_status'],

                        filterable:  ['license_date'],

                        requestAdapter(data) {

                            return {

                                'sort_field' : data.orderBy ? data.orderBy : 'license_id',

                                'sort_order' : data.ascending ? 'desc' : 'asc',

                                'search_query' : data.query.trim(),

                                perPage : data.limit,
                            }
                        },

                        columnsClasses: {

                            product_title: 'product_title',

                            license_date: 'license_date',

                            license_expire_date : 'license_expire_date',

                            license_updates_date: 'license_updates_date',

                            license_support_date: 'license_support_date',

                            installation_counts: 'installation_counts',

                            latest_call_backs: 'latest_call_backs',

                            license_status: 'license_status',

                            actions:      'actions',
                        },

                        templates: {

                            license_date(h, row) {

                                return formatDateTime(row.license_date, timezone, date_format, time_format)
                            },

                            license_expire_date(h, row) {

                                return formatDateTime(row.license_expire_date, timezone, date_format, time_format)
                            },

                            license_updates_date(h, row) {

                                return formatDateTime(row.license_updates_date, timezone, date_format, time_format)
                            },

                            license_support_date(h, row) {

                                return formatDateTime(row.license_support_date, timezone, date_format, time_format)
                            },

                            latest_call_backs(h, row) {

                                return formatDateTime(row.latest_call_backs, timezone, date_format, time_format)
                            },

                            product_title: (f, row) => {

                                if(row.product_title && row.product_id) {

                                    return h(RouterLink, {

                                        to: '/products/' + row.product_id + '/view'

                                    },[row.product_title])

                                } else {
                                    return '----'
                                }
                            },

                            license_status: (f, row) => {

                                return h('span', {
                                    'class': row.license_status ? 'text-green' : 'text-red'
                                }, row.license_status ? this.lang('active'): this.lang('inactive'))
                            },

                        },

                        pagination: { show : false },

                        headings: {

                            product_title: this.lang('product'),

                            license_date: this.lang('activation_date'),

                            license_expire_date : this.lang('expiration_date'),

                            license_updates_date: this.lang('updates_expiry'),

                            license_support_date: this.lang('support_expiry'),

                            installation_counts: this.lang('no_of_installations'),

                            latest_call_backs: this.lang('latest_callbacks'),

                            license_status: this.lang('status'),

                            actions: this.lang('actions')
                        },

                        responseAdapter({data}) {

                            return {

                                data: data.data.data.map(data => {

                                    data.edit_url = '/licenses/' + data.license_id + '/edit';

                                    data.delete_url = '/api/admin/license/delete';

                                    data.view_url = '/licenses/' + data.license_id + '/view';

                                    data.keyVal = 'license_id';

                                    data.idVal = data.license_id;

                                    return data;
                                }),
                                count: data.data.total
                            }
                        },
                    }

                this.loading = false

            }
        },

        titleChange() {

            return this.iconClassEmail === 'fas fa-copy' ? lang('copy_email') : lang('copied')
        }

    }
}
</script>

<style scoped>

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

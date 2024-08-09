<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View existing API keys. If any API key needs to be modified, click the API secret. If any API key needs
                to be deleted, check the API secret and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_api_keys')}}</h3>

                <div class="card-tools">

                    <router-link to="/apikeys/create" class="btn-tool" v-tooltip="lang('create_api_key')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="api_key_index">

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="products-list">

                </data-table>

            </div>
        </div>
    </div>
</template>

<script>

import {lang} from "../../helpers/extraLogics";
import {h} from 'vue'
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";

export default {

    name: 'api-keys',

    methods : {

        lang
    },

    data() {

        return {

            loading : false,

            data: '',

            columns: ['api_key_secret','api_key_description', 'api_key_ip', 'api_key_products', 'api_key_clients', 'api_key_licenses',
                'api_key_installations_edit', 'api_key_search', 'api_key_status','actions'],

            options: {},

            counter: 0,

            endPoint : '/api/admin/viewApiKeys?page=1'
        }
    },

    beforeMount() {

        const self = this;

        function createPermissionStatusLabel(h, hasPrmission) {
            return h('span', {
                attrs: {
                    'class': hasPrmission ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                }
            }, hasPrmission ? 'Active' : 'Inactive')
        }

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            sortable:  ['api_key_secret', 'api_key_ip', 'api_key_products',
                'api_key_clients', 'api_key_licenses', 'api_key_installations_edit',
                'api_key_search', 'api_key_status', 'api_key_description'],

            filterable : [ 'api_key_secret' ],

            requestAdapter(data) {

                return {

                    'sort_field' : data.orderBy ? data.orderBy : 'api_key_id',

                    'sort_order' : data.ascending ? 'desc' : 'asc',

                    'search_query' : data.query.trim(),

                     perPage : data.limit,
                }
            },

            responseAdapter({data}) {

                return {

                    data: data.data.data.map(data => {

                        data.edit_url = '/apikeys/' + data.api_key_id + '/edit';

                        data.delete_url = `/api/admin/deleteapi/${data.api_key_id}`;

                        data.keyVal = 'product_id';

                        data.idVal = data.product_id;

                        return data;
                    }),

                    count: data.data.total
                }
            },

            columnsClasses: {

                api_key_secret: 'api_key_secret',

                api_key_ip: 'api_key_ip',

                api_key_products: 'api_key_products',

                api_key_clients: 'api_key_clients',

                api_key_licenses: 'api_key_licenses',

                api_key_installations_edit: 'api_key_installations_edit',

                api_key_search: 'api_key_search',

                api_key_status: 'api_key_status',

                api_key_description :    'api_key_description',
            },

            templates: {

                api_key_secret(createElement, row) {

                    return row.api_key_secret ? row.api_key_secret : '---';
                },

                api_key_ip(h, row) {

                    return row.api_key_ip ? row.api_key_ip : '---';
                },

                api_key_description(h,row) {

                    return row.api_key_description ? row.api_key_description :  '---';
                },

                api_key_products(f, row) {
                    return h('span', {}, [`${row.api_key_products_add ? this.lang('active') : this.lang('inactive')}/${row.api_key_products_edit ? this.lang('active') : this.lang('inactive')}`])
                },

                api_key_clients(f, row) {
                    return h('span', {}, [`${row.api_key_clients_add ? this.lang('active') : this.lang('inactive')}/${row.api_key_clients_edit ? this.lang('active') : this.lang('inactive')}`])
                },

                api_key_licenses(f, row) {
                    return h('span', {}, [`${row.api_key_licenses_add ? this.lang('active') : this.lang('inactive')}/${row.api_key_licenses_edit ? this.lang('active') : this.lang('inactive')}`])
                },

                api_key_installations_edit: (f, row) => {

                    return h('span', {
                        'class': row.api_key_installations_edit ? 'text-green' : 'text-red'
                    }, row.api_key_installations_edit ? this.lang('active'): this.lang('inactive'))
                },

                api_key_search: (f, row) => {

                    return h('span', {
                        'class': row.api_key_search ? 'text-green' : 'text-red'
                    }, row.api_key_search ? this.lang('active'): this.lang('inactive'))
                },

                api_key_status: (f, row) => {

                    return h('span', {
                        'class': row.api_key_status ? 'text-green' : 'text-red'
                    }, row.api_key_status ? this.lang('active'): this.lang('inactive'))
                },


            },

            pagination: { show : false },

            headings: {

                api_key_secret: this.lang('api_secret'),

                api_key_ip: this.lang('ip_address'),

                api_key_products: this.lang('api_key_products'),

                api_key_clients: this.lang('api_key_clients'),

                api_key_licenses: this.lang('api_key_licenses'),

                api_key_installations_edit: this.lang('edit_installations'),

                api_key_search: this.lang('search'),

                api_key_status: this.lang('status'),

                api_key_description:  this.lang('description'),

                actions: this.lang('actions')
            },
        }
    },

    components : {

        'data-table' : DynamicDataTable
    }
};
</script>

<style>
#api_key_index .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

#api_key_index .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

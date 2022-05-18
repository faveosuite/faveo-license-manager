<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View existing API keys. If any API key needs to be modified, click the API secret. If any API key needs to be deleted, check the API secret and click the 'Submit' button.</p>
        </div>

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

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name : 'api-keys',

    data() {

        return {

            data : '',

            columns: ['api_secret', 'ip_address', 'add_edit_products', 'add_edit_clients', 'add_edit_licenses',
                'edit_installations', 'search', 'actions'],

            options: {},

            counter : 0
        }
    },

    created() {

        window.eventHub.$on('refreshData',this.updateData);
    },

    beforeMount(){

        const self= this;

        this.getData();

        this.options = {

            sortIcon: {

                base : 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses : {

                api_secret : 'api_secret',

                ip_address: 'ip_address',

                add_edit_products : 'add_edit_products',

                add_edit_clients: 'add_edit_clients',

                add_edit_licenses : 'add_edit_licenses',

                edit_installations : 'edit_installations',

                search : 'search',
            },

            templates : {

                api_secret(createElement, row) {

                    return row.api_secret ? row.api_secret : '---';
                },

                ip_address(h,row){

                    return row.ip_address ? row.ip_address : '---';
                },

                add_edit_products(h,row){

                    return row.add_edit_products ? row.add_edit_products : '---'
                },

                add_edit_clients(h,row) {

                    return row.add_edit_clients ? row.add_edit_clients : '---';
                },

                add_edit_licenses(h,row) {

                    return row.add_edit_licenses ? row.add_edit_licenses : '---';
                },

                edit_installations(h,row) {

                    return row.edit_installations ? row.edit_installations : '---';
                },

                search(h,row) {

                    return row.search ? row.search : '---';
                },

                status(createElement, row) {

                    let span = createElement('span', {

                        attrs: {
                            'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                        }
                    }, row.status ? 'Active' : 'Inactive');

                    return createElement('a',{},[span]);
                },

                actions : 'table-actions'
            },

            pagination:{chunk:5,nav: 'fixed',edge:true},

            headings: {

                api_secret: 'API Secret',

                ip_address: 'IP Address',

                add_edit_products: 'Add/Edit Products',

                add_edit_clients: 'Add/Edit Clients',

                add_edit_licenses : 'Add/Edit Licenses',

                edit_installations : 'Edit Installations',

                search: 'Search',
            },
        }
    },

    methods : {

        updateData() {

            this.getData();
        },

        getData() {

            axios.get('/api/admin/viewApiKeys').then(res=>{

                this.data = res.data.data.map(data => {

                    data.edit_url = 'editnewapi/{api_key_id}';

                    data.delete_url = '/api/admin/deleteapi/{api_key_id}';

                    return data;
                })
            })
        }
    }
};
</script>

<style>

#api_key_index .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#api_key_index .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

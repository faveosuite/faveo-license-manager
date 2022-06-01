<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View existing API keys. If any API key needs to be modified, click the API secret. If any API key needs
                to be deleted, check the API secret and click the 'Submit' button.</p>
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

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';

    export default {

        name: 'api-keys',

        data() {

            return {

                data: '',

                columns: ['api_secret', 'ip_address', 'add_edit_products', 'add_edit_clients', 'add_edit_licenses',
                    'edit_installations', 'search', 'actions'],

                options: {},

                counter: 0
            }
        },

        created() {

            window.eventHub.$on('refreshData', this.updateData);
        },

        beforeMount() {

            const self = this;

            this.getData();

            function createPermissionStatusLabel(h, hasPrmission) {
                return h('span', {
                    attrs: {
                        'class': hasPrmission ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                    }
                }, hasPrmission ? 'Active' : 'Inactive')
            };


            this.options = {

                sortIcon: {

                    base: 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                columnsClasses: {

                    api_secret: 'api_secret',

                    ip_address: 'ip_address',

                    add_edit_products: 'add_edit_products',

                    add_edit_clients: 'add_edit_clients',

                    add_edit_licenses: 'add_edit_licenses',

                    edit_installations: 'edit_installations',

                    search: 'search',
                },

                templates: {

                    api_secret(createElement, row) {

                        // return createElement('router-link', {
                        //     attrs: {
                        //         to: '/apikey/' + row.api_key_id+'/edit'
                        //     }
                        // }, row.api_key_secret);

                        return row.api_key_secret ? row.api_key_secret : '---';
                    },

                    ip_address(h, row) {

                        return row.api_key_ip ? row.api_key_ip : '---';
                    },

                    add_edit_products(h, row) {

                        const add = 'api_key_products_add' in row ? createPermissionStatusLabel(h, row.api_key_products_add) : '---'

                        const edit = 'api_key_products_edit' in row ? createPermissionStatusLabel(h, row.api_key_products_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    add_edit_clients(h, row) {

                        const add = 'api_key_clients_add' in row ? createPermissionStatusLabel(h, row.api_key_clients_add) : '---'

                        const edit = 'api_key_clients_edit' in row ? createPermissionStatusLabel(h, row.api_key_clients_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    add_edit_licenses(h, row) {
                        const add = 'api_key_licenses_add' in row ? createPermissionStatusLabel(h, row.api_key_licenses_add) : '---'

                        const edit = 'api_key_licenses_edit' in row ? createPermissionStatusLabel(h, row.api_key_licenses_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    edit_installations(h, row) {
                        const edit = 'api_key_installations_edit' in row ? createPermissionStatusLabel(h, row.api_key_installations_edit) : '---'
                        return h('div', {}, [edit])
                    },

                    search(h, row) {

                        const edit = 'api_key_search' in row ? createPermissionStatusLabel(h, row.api_key_search) : '---'

                        return h('div', {}, [edit])

                    },

                    actions: 'table-actions'
                },

                pagination: { chunk: 5, nav: 'fixed', edge: true },

                headings: {

                    api_secret: 'API Secret',

                    ip_address: 'IP Address',

                    add_edit_products: 'Add/Edit Products',

                    add_edit_clients: 'Add/Edit Clients',

                    add_edit_licenses: 'Add/Edit Licenses',

                    edit_installations: 'Edit Installations',

                    search: 'Search',

                    actions: 'Actions'
                },
            }
        },

        methods: {

            updateData() {

                this.getData();
            },

            getData() {

                axios.get('/api/admin/viewApiKeys').then(res => {

                    this.data = res.data.data.map(data => {

                        data.edit_url = '/apikeys/' + data.api_key_id + '/edit';

                        data.delete_url = `/api/admin/deleteapi/${data.api_key_id}`;

                        return data;
                    })
                })
            }
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
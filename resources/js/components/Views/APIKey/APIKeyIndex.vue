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

                columns: ['api_key_secret', 'api_key_ip', 'api_key_clients_add', 'api_key_clients_edit', 'api_key_licenses_add',
                    'api_key_installations_edit', 'api_key_search', 'actions'],

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
            }

            this.options = {

                sortIcon: {

                    base: 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                columnsClasses: {

                    api_key_secret: 'api_key_secret',

                    api_key_ip: 'api_key_ip',

                    api_key_clients_add: 'api_key_clients_add',

                    api_key_clients_edit: 'api_key_clients_edit',

                    api_key_licenses_add: 'api_key_licenses_add',

                    api_key_installations_edit: 'api_key_installations_edit',

                    api_key_search: 'api_key_search',
                },

                templates: {

                    api_key_secret(createElement, row) {

                        // return createElement('router-link', {
                        //     attrs: {
                        //         to: '/apikey/' + row.api_key_id+'/edit'
                        //     }
                        // }, row.api_key_secret);

                        return row.api_key_secret ? row.api_key_secret : '---';
                    },

                    api_key_ip(h, row) {

                        return row.api_key_ip ? row.api_key_ip : '---';
                    },

                    api_key_clients_add(h, row) {

                        const add = 'api_key_products_add' in row ? createPermissionStatusLabel(h, row.api_key_products_add) : '---'

                        const edit = 'api_key_products_edit' in row ? createPermissionStatusLabel(h, row.api_key_products_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    api_key_clients_edit(h, row) {

                        const add = 'api_key_clients_add' in row ? createPermissionStatusLabel(h, row.api_key_clients_add) : '---'

                        const edit = 'api_key_clients_edit' in row ? createPermissionStatusLabel(h, row.api_key_clients_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    api_key_licenses_add(h, row) {
                        const add = 'api_key_licenses_add' in row ? createPermissionStatusLabel(h, row.api_key_licenses_add) : '---'

                        const edit = 'api_key_licenses_edit' in row ? createPermissionStatusLabel(h, row.api_key_licenses_edit) : '---'

                        return h('div', {}, [add, ' / ', edit])
                    },

                    api_key_installations_edit(h, row) {
                        const edit = 'api_key_installations_edit' in row ? createPermissionStatusLabel(h, row.api_key_installations_edit) : '---'
                        return h('div', {}, [edit])
                    },

                    api_key_search(h, row) {

                        const edit = 'api_key_search' in row ? createPermissionStatusLabel(h, row.api_key_search) : '---'

                        return h('div', {}, [edit])

                    },

                    actions: 'table-actions'
                },

                pagination: { chunk: 5, nav: 'fixed', edge: true },

                headings: {

                    api_key_secret: 'API Secret',

                    api_key_ip: 'IP Address',

                    api_key_clients_add: 'Add/Edit Products',

                    api_key_clients_edit: 'Add/Edit Clients',

                    api_key_licenses_add: 'Add/Edit Licenses',

                    api_key_installations_edit: 'Edit Installations',

                    api_key_search: 'Search',

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
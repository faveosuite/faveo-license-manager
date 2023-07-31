<template>

    <div class="col-sm-12">



        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('users')}}</h3>

                <div class="card-tools">

                    <router-link to="/users/create" class="btn-tool" v-tooltip="lang('create_user')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="my_clients">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:client_status="props">

                        <span :class="props.row.client_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.client_status ? 'Active' : 'Inactive'}}
                        </span>
                    </template>

                    <template v-slot:actions="props">

                        <table-actions :data="props.row"></table-actions>
                    </template>
                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name: 'clients-list',

    data() {

        return {

            data: '',

            columns: ['full_name', 'user_email', 'user_active_date', 'user_status', 'actions'],

            options: {},

            counter: 0
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        const self = this;

        this.getData();

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses: {

                full_name: 'full_name',

                user_email: 'user_email',

                user_active_date: 'user_date',

                user_status: 'user_status'
            },

            templates: {},

            pagination: { chunk: 5, nav: 'fixed', edge: true },

            headings: {

                full_name: 'Full Name',

                user_email: 'Email',

                user_active_date: 'Active Date',

                user_status: 'Status',

                actions: 'Actions'
            },
        }
    },

    methods: {

        updateData() {

            this.getData();
        },

        getData() {

            this.loading = true;

            axios.get('users').then(res => {

                this.loading = false;

                this.data = res.data.data.map(data => {

                    data.edit_url = '/users/' + data.user_id + '/edit';

                    data.delete_url = '/deleteusers';

                    data.keyVal ='user_id';

                    data.idVal =data.user_id;
                    return data;
                })
            }).catch(err => {

                this.loading = false;
            })
        }
    }
};
</script>

<style>
.client_name,
.client_email,
.client_date,
.client_status {
    max-width: 200px;
    word-break: break-all;
}

#my_clients .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

#my_clients .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

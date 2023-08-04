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

            <div class="card-body" id="my_users">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:admin_status="props">
  <span :style="{ color: props.row.admin_status ? 'green' : 'red' }">
    {{ props.row.admin_status ? 'Active' : 'Inactive' }}
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

    name: 'admin-list',

    data() {

        return {

            data: '',

            columns: ['admin_fname', 'admin_email', 'admin_date', 'admin_status', 'actions'],

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

                admin_fname: 'admin_fname',

                admin_email: 'admin_email',

                admin_date: 'admin_date',

                admin_status: 'admin_status'
            },

            templates: {},

            pagination: { chunk: 5, nav: 'fixed', edge: true },

            headings: {

                admin_fname: 'Name',

                user_email: 'Email',

                admin_date: 'Date',

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

            this.loading =true;

            axios.get('/api/admin/users').then(res => {

                this.loading = false;

                this.data = res.data.data.map(data => {

                    data.edit_url = '/users/' + data.admin_id + '/edit';

                    data.delete_url = '/api/admin/deleteusers/'+ data.admin_id;

                    data.keyVal = 'admin_id';

                    data.idVal = data.admin_id;

                    return data;
                })
            }).catch(err => {
                this.loading =false;
            })
}
    }
};
</script>
<style>

#my_users .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

#my_users .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>


<template>

    <div class="col-sm-12">

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('users')}}</h3>

                <div class="card-tools">
                    <!-- Navigate to the user creation route -->

                    <router-link to="/users/create" class="btn-tool" v-tooltip="lang('create_user')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="my_users">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"  :show_pagination="showPagination">

                    <template v-slot:admin_status="props">

           {{ props.row.admin_status ? 'Active' : 'Inactive' }}

                    </template>
                    <!-- Template for displaying action buttons -->

                    <template v-slot:actions="props">

                        <table-actions :data="props.row"></table-actions>
                    </template>
                </v-client-table>

                <pagination v-if="showPagination" :prev_page_url="prev_page_url" :next_page="next_page_url" :on-pagination="onPagination"></pagination>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
import pagination from "../../components/Reusable/Pagination.vue";

export default {

    name: 'admin-list',
    // Data properties of the component

    data() {

        return {

            data: '',

            columns: ['admin_fname', 'admin_email', 'admin_date', 'admin_status', 'actions'],

            options: {},

            counter: 0,

            next_page_url : '',

            prev_page_url : '',

            limit : '',

            showPagination: true,
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        const self = this;
        // Fetch initial data

        this.getData();
        // Configure options for v-client-table

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

        onPagination() {
           this.getData(this.next_page_url, { limit : this.per_page })
        },

     getData(endpoint = '/api/admin/users', params = {}) {

            this.loading =true;
         // Fetch user data from the API

         axios.get(endpoint, params).then(res => {

                this.loading = false;
             // Map and manipulate fetched data

                this.data = res.data.data.data.map(data => {

                    data.edit_url = '/users/' + data.admin_id + '/edit';

                    data.delete_url = '/api/admin/deleteusers/'+ data.admin_id;

                    data.keyVal = 'admin_id';

                    data.idVal = data.admin_id;

                    return data;
                })

                this.limit = res.data.data.data.per_page;

                this.next_page_url = res.data.data.data.next_page_url;

                this.prev_page_url = res.data.data.data.prev_page_url;

            }).catch(err => {
                this.loading =false;
            })
}
    },

    components : {
        pagination,
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


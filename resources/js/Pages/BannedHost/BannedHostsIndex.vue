<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>View existing banned hosts. If any banned host needs to be modified, click the IP address. If any banned
                host needs to be deleted, check the IP address and click the 'Submit' button.</span>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_banned_hosts')}}</h3>

                <div class="card-tools">

                    <router-link to="/banned-hosts/create" class="btn-tool" v-tooltip="lang('create_banned_host')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="banned_hosts">

<!--                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">-->
<!--                    -->
<!--                    <template v-slot:actions="props">-->
<!--                        <table-actions :data="props.row"></table-actions>-->
<!--                    </template>-->
<!--                </v-client-table>-->

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="products-list">

                </data-table>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';
    import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";

    export default {

        name: 'banned-hosts',

        data() {

            return {

                loading : false,

                data: '',

                columns: ['banned_host_ip', 'banned_host_comments', 'banned_host_date','actions'],

                options: {},

                counter: 0,

                endPoint : '/api/admin/viewBannedHost?page=1'
            }
        },

        created() {

            this.emitter.on('refreshData', this.updateData);
        },

        async beforeMount() {

            const self = this;

            this.options = {

                sortIcon: {

                    base: 'glyphicon',

                    up: 'glyphicon-chevron-up',

                    down: 'glyphicon-chevron-down'
                },

                texts: { filter: '', limit: '' },

                sortable:  ['banned_host_ip', 'banned_host_comments', 'banned_host_date', 'banned_host_blocks', 'banned_host_last_block_date'],

                filterable : [ 'banned_host_ip' ],

                requestAdapter(data) {

                    return {

                        'sort_field' : data.orderBy ? data.orderBy : '',

                        'sort_order' : data.ascending ? 'asc' : 'desc',

                        'search_query' : data.query,

                         perPage : data.limit,
                    }
                },

                responseAdapter({data}) {

                    return {

                        data: data.data.data.map(data => {

                            data.edit_url = '/banned-hosts/' + data.banned_host_id + '/edit';

                            data.delete_url = '/api/admin/bannedHosts/delete';

                            data.keyVal = 'banned_host_id';

                            data.idVal = data.banned_host_id;

                            return data;
                        }),

                        count: data.data.total
                    }
                },

                columnsClasses: {

                    banned_host_ip: 'banned_host_ip',

                    banned_host_comments: 'banned_host_comments',

                    banned_host_date: 'banned_host_date	',
                },

                templates: {

                    banned_host_ip(h, row) {

                        return row.banned_host_ip ? row.banned_host_ip : '---';
                    },

                    banned_host_comments(h, row) {

                        return row.banned_host_comments ? row.banned_host_comments : '---'
                    },

                    banned_host_date(h, row) {

                        return row.banned_host_date ? row.banned_host_date : '---';
                    },
                },

                pagination: { show : false },

                headings: {

                    banned_host_ip: 'IP Address',

                    banned_host_comments: 'Comments',

                    banned_host_date: 'Date',

                    actions: 'Actions'
                },
            }
        },

        components : {

            'data-table' : DynamicDataTable
        }
    };
</script>

<style>
    #banned_hosts .VueTables .table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
    }

    #banned_hosts .VueTables .table-responsive>table {
        width: max-content;
        min-width: 100%;
        max-width: max-content;
        overflow: auto !important;
    }
</style>

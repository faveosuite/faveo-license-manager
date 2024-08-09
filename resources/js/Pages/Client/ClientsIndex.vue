<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal"/>

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{ lang('all_contacts') }}</h3>

                <div class="card-tools">

                    <router-link to="/clients/create" class="btn-tool" v-tooltip="lang('create_client')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="my_clients">

                <data-table :url="endPoint" :show_pagination="true" alertComponentName="dataTableModal" :dataColumns="columns" :option="options">

                </data-table>

            </div>
        </div>
    </div>
</template>

<script>

import {useStore} from "vuex";
import {computed, h} from "vue";
import {formatDateTime, lang} from "../../helpers/extraLogics";
import { RouterLink } from 'vue-router';
import DataTableStatuses from "../../components/Reusable/DataTableStatuses.vue";
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";

export default {
    setup() {

        const store = useStore();

        return {
            getUserData: computed(() => store.getters.getUserData)
        };
    },
    data() {

        return {

            loading : false,

            data: '',

            columns: ['full_name', 'client_email', 'client_role', 'client_active_date','info', 'client_status', 'actions'],

            options: {},

            counter: 0,

            disabled: true,

            endPoint : `/api/admin/viewClients/${this.getUserData.client_id}?page=1`
        }
    },

    props : {

        generalSetting : {type : Object, default : () => {}},
    },

    created() {

        this.emitter.on('refreshData', this.getData);
    },

    beforeMount() {

        const self = this;

        const date_format = this.generalSetting.date_format.js_format;
        const time_format = this.generalSetting.time_format.js_format;
        const timezone = this.generalSetting.timezone.name;

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: {filter: '', limit: ''},

            columnsClasses: {

                full_name: 'full_name',

                client_email: 'client_email',

                client_active_date: 'client_date',

                info: 'info',

                client_status: 'client_status',

                client_role: 'client_role'
            },

            templates: {

                client_active_date(h, row) {

                    return formatDateTime(row.client_active_date, timezone, date_format, time_format)
                },

                info : (f,row)=> {

                    return h(DataTableStatuses, {data: row})
                },

                full_name: (f, row) => {

                    if(row.full_name) {

                        return h(RouterLink, {

                            to: '/clients/' + row.client_id + '/view'

                        },[row.full_name])

                    } else {
                        return '----'
                    }
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

                client_status: (f, row) => {

                    return h('span', {
                        'class': row.client_status ? 'text-green' : 'text-red'
                    }, row.client_status ? this.lang('active'): this.lang('inactive'))
                },
            },

            pagination: { show : false },

            sortable:  ['full_name', 'client_email', 'client_role', 'client_active_date', 'client_status'],

            filterable : [ 'full_name' ],

            requestAdapter(data) {

                return {

                    'sort_field' : data.orderBy ? data.orderBy : 'client_id',

                    'sort_order' : data.ascending ? 'desc' : 'asc',

                    'search_query' : data.query.trim(),

                     perPage : data.limit,
                }
            },

            responseAdapter({data}) {

                return {

                    data: data.data.data.map(data => {

                        data.edit_url = '/clients/' + data.client_id + '/edit';

                        data.delete_url = '/api/admin/clients/delete';

                        data.view_url = '/clients/' + data.client_id + '/view';

                        data.keyVal = 'client_id';

                        data.idVal = data.client_id;

                        return data;
                    }),

                    count: data.data.total
                }
            },

            headings: {

                full_name: 'Name',

                client_email: 'Email',

                info: 'Info',

                client_active_date: 'Activation Date',

                client_status: 'Status',

                client_role: 'Role',

                actions: 'Actions'
            },
        }
    },

    methods: {

        lang: lang,

    },

    components : {

        'data-table' : DynamicDataTable
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

.client_role {
    text-transform: capitalize;
}
 #my_clients .VueTables .table-responsive>table {
 width: max-content;
 min-width: 100%;
 max-width: max-content;
 overflow: auto !important;
 }
 .VueTables .table-responsive > table th {
     position: static !important;
 }
#my_clients .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}
</style>

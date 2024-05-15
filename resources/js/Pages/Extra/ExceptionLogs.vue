<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('error_logs')}}</h3>

                <div class="card-tools">

                    <router-link to="/licenses/create" class="btn-tool" v-tooltip="lang('create_license')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="my_licenses">

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="licenses-list">

                </data-table>

            </div>

        </div>
    </div>
</template>

<script>

import axios from 'axios';
import {lang} from "../../helpers/extraLogics";
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import moment from "moment";
import {useStore} from 'vuex';
import {computed} from "vue";

export default {

    name: 'exception-logs',

    // setup() {
    //
    //     const store = useStore();
    //
    //     return {
    //
    //         formattedTime : computed(()=>store.getters.formattedTime)
    //     }
    // },

    data() {

        return {

            loading: false,

            data: '',

            columns: ['category', 'file', 'line', 'message', 'trace', 'created_at'],

            options: {},

            counter: 0,

            endPoint : '/api/admin/logs/exception?page=1',
        }
    },

    beforeMount() {

        const self = this;

        // let date = this.created_at ? moment(this.created_at).format('YYYY-MM-DD') : '';
        //
        // console.log(date)

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            headings: {

                category: this.lang('category'),

                file: this.lang('file'),

                line: this.lang('line'),

                message: this.lang('message'),

                trace: this.lang('trace'),

                created_at: this.lang('created-at')
            },

            texts: { filter: '', limit: '' },

            sortable:  ['category','file', 'line', 'message', 'trace', 'created_at'],

            filterable:  ['category','file', 'line', 'message', 'trace', 'created_at'],

            requestAdapter(data) {
                console.log('request', data)

                return {

                    'sort_field' : data.orderBy ? data.orderBy : 'id',

                    'sort_order' : data.ascending ? 'asc' : 'desc',

                    'search_query' : data.query,

                    // page : data.page,

                    perPage : data.limit,
                }
            },

            responseAdapter({data}) {
                console.log('response',data);
                return {

                    data: data.data.data,

                    count: data.data.total
                }
            },

            columnsClasses : {

                category: 'log-category',

                file: 'log-file',

                line:'log-line',

                trace: 'log-trace',

                message: 'log-message',

                created_at: 'log-created'
        },

            templates: {

                category(h,row){

                    return row.category.name
                },

                created_at(h, row) {

                    return row.created_at
                },

                // trace: (f,row)=>{
                //
                //     return self.h(LogsTrace,{ data : row })
                // },
            },

            pagination: { show : false },
        }
    },

    components : {

        'data-table' : DynamicDataTable
    }
};
</script>

<style>
.license_product_title,
.license_code,
.license_install,
.license_callbacks,
.latest_callback_time,
.license_date {
    max-width: 200px;
    word-break: break-all;
}

#my_licenses .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#my_licenses .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

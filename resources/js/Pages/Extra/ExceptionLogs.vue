<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('exception_logs')}}</h3>

            </div>

            <div class="card-body" id="exception-logs">

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="exception-logs">

                </data-table>

            </div>

        </div>
    </div>
</template>

<script>

import {formatDateTime, lang} from "../../helpers/extraLogics";
import {h} from 'vue'
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import moment from "moment";
import LogsTrace from "../../components/Reusable/LogsTrace.vue";
import 'moment-timezone'

export default {

    name: 'exception-logs',

    methods: {

        lang
    },

    props : {
        generalSetting : {type : Object, default : () => {}},
    },

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

        const date_format = this.generalSetting.date_format.js_format
        const time_format = this.generalSetting.time_format.js_format
        const timezone = this.generalSetting.timezone.name

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            headings: {

                category: lang('category'),

                file: lang('file'),

                line: lang('line'),

                message: lang('message'),

                trace: lang('trace'),

                created_at: lang('created_at')
            },

            texts: { filter: '', limit: '' },

            sortable:  ['category','file', 'line', 'message', 'trace', 'created_at'],

            filterable:  ['category','file', 'line', 'message', 'trace', 'created_at'],

            requestAdapter(data) {

                return {

                    'sort_field' : data.orderBy ? data.orderBy : 'id',

                    'sort_order' : data.ascending ? 'desc' : 'asc',

                    'search_query' : data.query.trim(),

                     perPage : data.limit,
                }
            },

            responseAdapter({data}) {

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

                    return formatDateTime(row.created_at, timezone, date_format, time_format)
                },

                trace: (f,row)=>{

                    return h(LogsTrace, { data : row })
                },
            },

            pagination: { show : false },
        }
    },

    components : {

        'data-table' : DynamicDataTable,
    }
};
</script>

<style>
.log-category,.log-file,.log-trace,.log-line,.log-created,.log-message
{ max-width: 250px; word-break: break-all;}

#exception-logs .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#exception-logs .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

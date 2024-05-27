<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light " id="my_crackingreports">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_cracking_reports')}}</h3>

            </div>

            <div class="card-body" id="my_licenses">

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="licenses-list">

                </data-table>

            </div>
        </div>
    </div>
</template>

<script>

import {lang} from '../../helpers/extraLogics'
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import moment from "moment";
import 'moment-timezone'

export default {

    name: 'licenses-list',

    methods: {
        lang
    },

    data() {

        return {

            loading : false,

            data: '',

            columns: ['report_text','license_code','report_date_time','report_status'],

            options: {},

            counter: 0,

            endPoint : '/api/admin/reportCracking?page=1'
        }
    },

    created() {

        // this.emitter.on('refreshData', this.updateData);
    },

    props : {
        generalSetting : {type : Object, default : () => {}},
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

            texts: { filter: '', limit: '' },

            sortable:  ['report_text', 'license_code', 'report_date_time', 'report_status'],

            filterable : [ 'report_text' ],

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

                        data.keyVal = 'product_id';

                        data.idVal = data.product_id;

                        return data;
                    }),

                    count: data.data.total
                }
            },

            columnsClasses: {

                product_title: 'license_product_title',

                license_code: 'license_code',

                account_id: 'account_id',

                report_date_time: 'report_date_time',

                report_text:  'report_text',
            },

            templates: {

                license_code(h, row) {

                    return row.license_code ? row.license_code : '---';
                },

                license_date(h, row) {

                    return row.license_date ? row.license_date : '---'
                },

                latest_callback_date_time(h, row) {

                    return row.latest_callback_date_time ? moment(row.latest_callback_date_time).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                },
            },

            pagination: { show : false },

            headings: {

                product_title: 'Product',

                report_id:   'Report',

                license_code: 'License Code',

                report_text:  'Report',

                report_date_time: 'Report Date Time',

            },
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

#my_crackingreports .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}
.VueTables .table-responsive>table th {
    white-space: nowrap;
    width: 200px;
}

#my_crackingreports .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}

#my_crackingreports .glyphicon-sort {
     margin-left: 0px;
   margin-top: 0px;
}
</style>



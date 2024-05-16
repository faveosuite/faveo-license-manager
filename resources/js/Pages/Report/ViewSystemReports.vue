<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light " id="my_systemreports">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_system_reports')}}</h3>

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
import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";

export default {

    name: 'licenses-list',

    data() {

        return {

            loading : false,

            data: '',

            columns: ['report_text' ,'user_formatted','report_date_time','report_status'],

            options: {},

            counter: 0,

            endPoint : '/api/admin/reportSystem?page=1'
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        const self = this;

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            sortable:  ['report_text', 'user_formatted', 'report_date_time', 'report_status'],

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

                report_status:  'Status',

                user_formatted:  'Format'
            },

            templates: {

                license_code(h, row) {

                    return row.license_code ? row.license_code : '---';
                },

                license_date(h, row) {

                    return row.license_date ? row.license_date : '---'
                },

                latest_callback_date_time(h, row) {

                    return row.latest_callback_date_time ? row.latest_callback_date_time : '---';
                },

            },

            pagination: { show : false },

            headings: {

                product_title: 'Product',

                license_code: 'License Code',

                report_text:  'Report',

                report_date_time: 'Report Date Time',

                report_status:  'Status',

                user_formatted:  'User',

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

#my_licenses .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}
.VueTables .table-responsive>table th {
    white-space: nowrap;
    width: 200px;
}

#my_licenses .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
#my_systemreports .glyphicon-sort {
    margin-left: 0px;
    margin-top: 0px;
}
</style>



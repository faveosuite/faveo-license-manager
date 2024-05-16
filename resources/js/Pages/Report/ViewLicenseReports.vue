<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light" id="my_licencereports">

            <div class="card-header">

                <h3 class="card-title">{{ lang('view_license_reports') }}</h3>
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

            data: '',

            columns: ['report_text', 'license_code', 'product_title','report_date_time', 'report_status'],

            options: {},

            counter: 0,

            loading: false, // Add the 'loading' property

            endPoint : '/api/admin/reportLicense?page=1'
        };
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down',
            },

            texts: { filter: '', limit: '' },

            sortable:  ['product_title', 'report_text', 'license_code', 'report_date_time', 'report_status'],

            filterable : [ 'product_title', 'report_text' ],

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

                report_date_time: 'report_date_time',

                report_text: 'report_text',

                report_status: 'Status',
            },

            pagination: { show : false },

            headings: {

                products_title: 'Product Title',

                license_code: 'License Code',

                report_text: 'Report',

                report_date_time: 'Report Date Time',

                report_status: 'Status',
            },

            templates :{

                license_code(h, row) {

                    return row.license_code ? row.license_code : '---';
                },

                product_title(h, row) {

                    return row.product.product_title ?? '---';
                }

            }
        };
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

.VueTables .table-responsive>table th {
    white-space: nowrap;
    width: 200px;
}
#my_licencereports .glyphicon-sort {
    margin-left: 0px;
    margin-top: 0px;
}
</style>

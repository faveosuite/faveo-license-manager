<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>View existing license verification callbacks. If any callback needs to be deleted, check the client or license code and click the 'Submit' button.</span>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal"/>

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('callbacks')}}</h3>
            </div>

            <div class="card-body" id="my_callbacks">

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="callbacks-list">

                </data-table>
            </div>
        </div>
    </div>
</template>

<script>

import {lang} from "../../helpers/extraLogics";

import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import moment from "moment";
import 'moment-timezone'

export default {

    name : 'callbacks-list',

    methods : {

        lang
    },

    props : {
        generalSetting : {type : Object, default : () => {}},
    },

    data() {

        return {

            data : '',

            columns: ['product_title', 'license_code','callback_ip','callback_domain',
                'callback_date_time','created_at','updated_at'],

            loading: false,

            options: {},

            counter : 0,

            endPoint : 'api/admin/showLicenseCallbacks?page=1'
        }
    },

    created() {

        // this.emitter.on('refreshData',this.updateData);
    },

    beforeMount(){

        const self= this;

        const date_format = this.generalSetting.date_format.js_format
        const time_format = this.generalSetting.time_format.js_format
        const timezone = this.generalSetting.timezone.name

        this.options = {

            sortIcon: {

                base : 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            sortable:  ['product_title', 'license_code', 'callback_ip', 'callback_domain', 'callback_date_time', 'created_at', 'updated_at'],

            filterable : [ 'product_title' ],

            columnsClasses : {

                product_title:        'license_product_title',

                license_code:         'license_code',

                callback_domain:      'callback_domain',

                callback_date_time:   'callback_date',

                created_at:            'Created_at',

                updated_at :           'Updated_at',

                client_formatted:     'Client_format',

                license_date:         'license_date',
            },

            templates : {

                product_title(h, row) {

                    return row.product.product_title ? row.product.product_title : '---'
                },

                // license_code(h, row) {
                //
                //     return row.license_code ? row.license_code : '---';
                // },

                // license_date(h, row) {
                //
                //     return row.license_date ? row.license_date : '---'
                // },
                created_at(h, row) {

                    return row.created_at ? moment(row.created_at).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                },
                updated_at(h, row) {

                    return row.updated_at ? moment(row.updated_at).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                },

                callback_date_time(h, row) {
                    return row.callback_date_time ? moment(row.callback_date_time).tz(timezone).format(`${date_format} ${time_format}`) : '----'

                }
                // latest_callback_date_time(h, row) {
                //
                //     return row.latest_callback_date_time ? row.latest_callback_date_time : '---';
                // },
            },

            pagination : { show : false },

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

                        return data;
                    }),
                    count: data.data.total
                }
            },

            headings: {

                product_id:           'Product',

                license_code:         'License Code',

                callback_ip:          'Callback IP',

                callback_domain:      'Callback Domain',

                callback_date_time:   'Callback Date',

                created_at:           'Created at',

                updated_at:           'Updated at',

                license_date:         'Latest License',

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

#my_callbacks .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

#my_callbacks .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{('licenses')}}</h3>

                <div class="card-tools">

                    <router-link to="/licenses/create" class="btn-tool" v-tooltip="('create_license')">

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

import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
import {useStore} from 'vuex';
import {computed} from "vue";

export default {

    name: 'licenses-list',

    setup() {

        const store = useStore();

        return {

            formattedTime : computed(()=>store.getters.formattedTime)
        }
    },

    data() {

        return {

            loading: false,

            data: '',

            columns: ['product_title', 'license_code', 'order_number', 'installations_count', 'callbacks_count',
                'latest_callback_date', 'latest_license_date','actions'],

            options: {},

            counter: 0,

            endPoint : '/api/admin/viewLicenses?page=1',
        }
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

            sortable:  ['product_title','license_code', 'order_number', 'installations_count', 'callbacks_count', 'latest_callback_date', 'latest_license_date'],

            filterable:  ['product_title'],

            requestAdapter(data) {

                return {

                    'sort_field' : data.orderBy ? data.orderBy : 'license_id',

                    'sort_order' : data.ascending ? 'asc' : 'desc',

                    'search_query' : data.query,

                     perPage : data.limit,
                }
            },

            responseAdapter({data}) {
                console.log('response',data);
                return {

                    data: data.data.data.map(data => {

                        data.edit_url = '/licenses/' + data.license_id + '/edit';

                        data.delete_url = '/api/admin/license/delete';

                        data.keyVal = 'license_id';

                        data.idVal = data.license_id;

                        return data;
                    }),
                    count: data.data.total
                }
            },

            columnsClasses: {

                product_title: 'license_product_title',

                license_code: 'license_code',

                order_number : 'order_number',

                installations_count: 'license_install',

                callbacks_count: 'license_callbacks',

                latest_callback_date: 'latest_callback_date',

                latest_license_date: 'latest_license',

                actions:      'actions',
            },

            templates: {
                latest_license(h,row){
                    return row.latest_license ? row.latest_license : '---';
                },

                latest_callback(h,row){
                    return row.latest_callback ? row.latest_callback : '---';
                },

                license_code(h, row) {
                    const formattedLicenseCode = row.license_code ? row.license_code.match(/.{1,4}/g).join('-') : '----';
                    return formattedLicenseCode;
                },

                // order_number(h, row) {
                //     const parser = new DOMParser();
                //     // Parse the HTML string
                //     const parsedHtml = parser.parseFromString(row.order_url, 'text/html');
                //     // Get the root element of the parsed HTML
                //     const htmlElement = parsedHtml.documentElement;
                //     const um = htmlElement.getElementsByTagName('a')
                //     // console.log(um[0].getAttribute('href'))
                //     console.log(um[0].textContent)
                //     return um[0].textContent ? um[0].textContent : '---'
                // },

                latest_license_date(h, row) {

                    return row.latest_license_date ? row.latest_license_date : '---'
                },

                latest_callback_date(h, row) {

                    return row.latest_callback_date ? row.latest_callback_date : '---';
                },

            },

            pagination: { show : false },

            headings: {

                product_id: 'Product',

                license_code: 'License Code',

                order_number : 'Order Number',

                installations_count: 'Installations',

                callbacks_count: 'Callbacks',

                latest_callback_date: 'Latest Callback',

                latest_license_date: 'Latest License',


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

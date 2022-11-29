<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_system_reports')}}</h3>

            </div>

            <div class="card-body" id="my_licenses">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:product_title="props">

                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>
                    </template>

                    <template v-slot:license_status="props">

                        <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.license_status ? 'Active' : 'Inactive'}}
                        </span>
                    </template>

                    <template v-slot:actions="props">

                        <table-actions :data="props.row"></table-actions>
                    </template>
                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name: 'licenses-list',

    data() {

        return {

            data: '',

            columns: ['product_id', 'report_id', 'license_code','report_date_time','report_text' ,'report_status','user_formatted','actions'],

            options: {},

            counter: 0
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        const self = this;

        this.getData();

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses: {

                product_id: 'license_product_title',

                report_id: 'report_id',

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

            pagination: { chunk: 5, nav: 'fixed', edge: true },

            headings: {

                product_id: 'Product',

                report_id:   'Report',

                license_code: 'License Code',

                report_text:  'Report',

                report_date_time: 'Report Date Time',

                report_status:  'Status',

                user_formatted:  'Format',

                actions: 'Actions'
            },
        }
    },

    methods: {

        updateData() {

            this.getData();
        },

        getData() {

            this.loading = true;

            axios.get('/api/admin/reportSystem').then(res => {

                this.loading = false;

                this.data = res.data.map(data => {

                    data.edit_url = '/licenses/' + data.license_id + '/edit';

                    data.delete_url = '/api/admin/license/delete';

                    data.keyVal = 'license_id';

                    data.idVal = data.license_id;

                    return data;
                })
            }).catch(err => {

                this.loading = false;
            })
        }
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

#my_licenses .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>


<!--<template>-->

<!--    <div class="col-sm-12">-->

<!--        <div class="alert alert-info">-->
<!--            <span>View license reports. If any report needs to be deleted, check the report and click the 'Submit' button.</span>-->
<!--        </div>-->

<!--        <div class="row" v-if="loading">-->

<!--            <custom-loader :duration="4000"></custom-loader>-->
<!--        </div>-->

<!--        <alert componentName="dataTableModal"/>-->

<!--        <div class="card card-light ">-->

<!--            <div class="card-header">-->

<!--                <h3 class="card-title">{{lang('view_system_reports')}}</h3>-->

<!--            </div>-->

<!--            <div class="card-body" id="my_system">-->

<!--                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">-->

<!--                    <template v-slot:product_title="props">-->

<!--                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>-->
<!--                    </template>-->

<!--                    <template v-slot:license_status="props">-->

<!--                        <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">-->

<!--                            {{ props.row.license_status ? 'Active' : 'Inactive'}}-->
<!--                        </span>-->
<!--                    </template>-->

<!--                    <template v-slot:actions="props">-->

<!--                        <table-actions :data="props.row"></table-actions>-->
<!--                    </template>-->
<!--                </v-client-table>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->

<!--<script>-->

<!--import axios from 'axios';-->

<!--export default {-->

<!--    name : 'system-report-list',-->

<!--    data() {-->

<!--        return {-->

<!--            data : '',-->

<!--            columns: ['product_title', 'license_code', 'total_installations', 'total_callbacks', 'license_date',-->
<!--                'latest_callback_date_time', 'actions'],-->
<!--            options: {},-->

<!--            counter : 0,-->

<!--            report_id : '',-->

<!--            loading : false-->
<!--        }-->
<!--    },-->

<!--    created() {-->

<!--        this.emitter.on('refreshData',this.updateData);-->
<!--    },-->

<!--    beforeMount(){-->

<!--        const self= this;-->

<!--         this.getData();-->

<!--        this.options = {-->

<!--            sortIcon: {-->

<!--                base : 'glyphicon',-->

<!--                up: 'glyphicon-chevron-up',-->

<!--                down: 'glyphicon-chevron-down'-->
<!--            },-->

<!--            texts: { filter: '', limit: '' },-->

<!--            columnsClasses : {-->

<!--                product_title: 'license_product_title',-->

<!--                license_code: 'license_code',-->

<!--                total_installations: 'license_install',-->

<!--                total_callbacks: 'license_callbacks',-->

<!--                latest_callback_date_time: 'latest_callback_time',-->

<!--                license_date: 'license_date',-->
<!--            },-->

<!--            templates : {-->
<!--                license_code(h, row) {-->

<!--                    return row.license_code ? row.license_code : '-&#45;&#45;';-->
<!--                },-->

<!--                license_date(h, row) {-->

<!--                    return row.license_date ? row.license_date : '-&#45;&#45;'-->
<!--                },-->

<!--                latest_callback_date_time(h, row) {-->

<!--                    return row.latest_callback_date_time ? row.latest_callback_date_time : '-&#45;&#45;';-->
<!--                },-->

<!--                user(h,row){-->

<!--                    return row.user ? row.user.report_date : '-&#45;&#45;';-->
<!--                },-->

<!--                report_status(createElement, row) {-->

<!--                    let span = createElement('span', {-->

<!--                        attrs: {-->
<!--                            'class' : row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'-->
<!--                        }-->
<!--                    }, row.report_status ? 'Active' : 'Inactive');-->

<!--                    return createElement('a',{},[span]);-->
<!--                },-->

<!--                actions : 'table-actions'-->
<!--            },-->

<!--            pagination:{chunk:5,nav: 'fixed',edge:true},-->

<!--            headings: {-->
<!--                product_id: 'Product',-->

<!--                license_code: 'License Code',-->

<!--                total_installations: 'Installations',-->

<!--                total_callbacks: 'Callbacks',-->

<!--                latest_callback_date_time: 'Latest Callback',-->

<!--                license_date: 'Latest License',-->

<!--                actions: 'Actions'-->
<!--            },-->
<!--        }-->
<!--    },-->

<!--    methods:{-->
<!--        updateData() {-->

<!--            this.getData();-->
<!--        },-->

<!--        getData() {-->

<!--            this.loading = true;-->

<!--            axios.get('/api/admin/reportSystem').then(res => {-->

<!--                this.loading = false;-->

<!--                this.data = res.data.map(data => {-->

<!--                    return data;-->
<!--                })-->
<!--            }).catch(err => {-->

<!--                this.loading = false;-->
<!--            })-->
<!--        }-->
<!--    }-->

<!--};-->
<!--</script>-->

<!--<style>-->

<!--.i_product_title,.i_license_code,.i_total_installations,.i_latest_installation,.i_installation_status{ max-width: 200px; word-break: break-all;}-->

<!--#my_system .VueTables .table-responsive {-->
<!--    overflow-x: auto;overflow-y: hidden;-->
<!--}-->

<!--#my_system .VueTables .table-responsive > table{-->
<!--    width : max-content;-->
<!--    min-width : 100%;-->
<!--    max-width : max-content;-->
<!--    overflow: auto !important;-->
<!--}-->
<!--</style>-->

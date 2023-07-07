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

                    <template v-slot:report_status="props">

                        <span :class="props.row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.report_status ? 'Active' : 'Inactive'}}
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

            columns: ['product_title', 'license_code','report_date_time','report_text' ,'report_status','user_formatted'],

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

                product_title: 'license_product_title',

                license_code: 'license_code',

                account_id: 'account_id',

                report_date_time: 'report_date_time',

                report_text:  'report_text',

                report_status:  'Status',

                user_formatted:  'Format'
            },

            templates: {

                product_title(createElement, row) {

                    if(row.product_id) {

                        return createElement('router-link', {
                            attrs: {
                                to: '/products/'+row.product_id+'/edit'
                            }
                        }, row.product_title);

                    } else{
                        return '---'
                    }
                },

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

                product_title: 'Product',

                license_code: 'License Code',

                report_text:  'Report',

                report_date_time: 'Report Date Time',

                report_status:  'Status',

                user_formatted:  'Format',

            },
        }
    },

    methods: {


        getData() {

            this.loading = true;

            axios.get('/api/admin/reportSystem').then(res => {

                this.loading = false;

                this.data = res.data.map(data => {

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
</style>



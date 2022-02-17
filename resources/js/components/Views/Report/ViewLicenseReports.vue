<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View license reports. If any report needs to be deleted, check the report and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal"/>

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_license_reports')}}</h3>

            </div>

            <div class="card-body" id="my_license">

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name : 'license-list',

    data() {

        return {

            data : '',

            columns: ['product_title', 'reports', 'latest_reports', 'report_status', 'actions'],

            options: {},

            counter : 0,

            license_id : '',

            loading : false
        }
    },

    created() {

        window.eventHub.$on('refreshData',this.updateData);
    },

    beforeMount(){

        const self= this;

        this.getData();

        this.options = {

            sortIcon: {

                base : 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses : {

                product_title : 'i_product_title',

                reports: 'i_reports',

                latest_reports : 'i_latest_reports',

                report_status : 'i_report_status',
            },

            templates : {

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

                latest_reports(h,row){

                    return row.latest_reports ? row.latest_reports.report_date : '---';
                },

                report_status(createElement, row) {

                    let span = createElement('span', {

                        attrs: {
                            'class' : row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                        }
                    }, row.report_status ? 'Active' : 'Inactive');

                    return createElement('a',{},[span]);
                },

                actions : 'table-actions'
            },

            pagination:{chunk:5,nav: 'fixed',edge:true},

            headings: {

                product_title: 'Product',

                reports: 'Reports',

                latest_reports : 'Latest Reports',

                report_status : 'Status',

                actions: 'Actions'
            },
        }
    },


};
</script>

<style>

.i_product_title,.i_license_code,.i_total_installations,.i_latest_installation,.i_installation_status{ max-width: 200px; word-break: break-all;}

#my_installations .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#my_installations .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

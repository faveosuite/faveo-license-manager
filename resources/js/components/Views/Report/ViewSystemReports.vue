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

                <h3 class="card-title">{{lang('view_system_reports')}}</h3>

            </div>

            <div class="card-body" id="my_system">

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name : 'system-report-list',

    data() {

        return {

            data : '',

            columns: ['report_title', 'user', 'report_date', 'report_status'],

            options: {},

            counter : 0,

            report_id : '',

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

                report_title : 'i_report_title',

                user : 'i_user',

                report_date : 'i_report_date',

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

                user(h,row){

                    return row.user ? row.user.report_date : '---';
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

                report_title : 'Report',

                user : 'User',

                report_date : 'Date',

                report_status : 'Status',
            },
        }
    },

};
</script>

<style>

.i_product_title,.i_license_code,.i_total_installations,.i_latest_installation,.i_installation_status{ max-width: 200px; word-break: break-all;}

#my_system .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#my_system .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

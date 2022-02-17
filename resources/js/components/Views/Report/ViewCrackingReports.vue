<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View cracking reports. If any report needs to be deleted, check the report and click the 'Submit' button.<br><br>
                <b>Attention</b>: all failed installations and verifications are displayed in View License Reports section; therefore, this section should always be empty. If you see any record here, most likely someone was sending invalid data to licensing server. If automatic hosts banning is enabled, Auto PHP Licenser will ban attacker automatically. Otherwise, you should ban attacker's host manually.</p>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal"/>

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_cracking_reports')}}</h3>

            </div>

            <div class="card-body" id="my_cracking">

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name : 'cracking-list',

    data() {

        return {

            data : '',

            columns: ['report_title', 'client_license', 'report_date', 'report_status'],

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

                client_license : 'i_client_license',

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

                latest_report(h,row){

                    return row.client_license ? row.client_license.report_date : '---';
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

                client_license : 'Client or License Code',

                report_date : 'Date',

                report_status : 'Status',
            },
        }
    },


};
</script>

<style>

.i_product_title,.i_license_code,.i_total_installations,.i_latest_installation,.i_installation_status{ max-width: 200px; word-break: break-all;}

#my_cracking .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#my_cracking .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

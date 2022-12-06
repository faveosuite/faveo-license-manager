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

                <div class="card-tools">

                    <router-link to="/callbacks/create" class="btn-tool" v-tooltip="lang('create_callback')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="my_installations">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name : 'callbacks-list',

    data() {

        return {

            data : '',

            columns: ['product_title', 'total_callbacks', 'latest_callbacks', 'callbacks_status', 'actions'],

            options: {},

            counter : 0,

            installation_id : '',

            loading : false
        }
    },

    created() {

        this.emitter.on('refreshData',this.updateData);
    },

    beforeMount(){

        const self= this;

        // this.getData();

        this.options = {

            sortIcon: {

                base : 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses : {

                product_title : 'i_product_title',

                total_callbacks : 'i_total_callbacks',

                latest_callback : 'i_latest_callback',

                callback_status : 'i_callback_status',
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

                latest_callback(h,row){

                    return row.latest_callback ? row.latest_callback.callback_date : '---';
                },

                callback_status(createElement, row) {

                    let span = createElement('span', {

                        attrs: {
                            'class' : row.callback_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                        }
                    }, row.callback_status ? 'Active' : 'Inactive');

                    return createElement('a',{},[span]);
                },

                actions : 'table-actions'
            },

            pagination:{chunk:5,nav: 'fixed',edge:true},

            headings: {

                product_title: 'Product',

                total_callbacks : 'Total Callbacks',

                latest_callbacks : 'Latest Callback',

                callbacks_status : 'Status',

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

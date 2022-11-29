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

            <div class="card-body" id="callbacks">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

import {lang} from "../../helpers/extraLogics";

export default {

    name : 'callbacks-list',

    data() {

        return {

            data : '',

            columns: ['product_title', 'license_code','callback_ip','callback_domain',
                'callback_date_time','created_at','updated_at'],

            loading: true,

            options: {},

            counter : 0,
        }
    },

    // created() {
    //
    //     this.emitter.on('refreshData',this.updateData);
    // },

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
                license_code(h, row) {

                    return row.license_code ? row.license_code : '---';
                },

                license_date(h, row) {

                    return row.license_date ? row.license_date : '---'
                },
                created_at(h, row) {

                    return row.license_date ? row.license_date : '---'
                },
                updated_at(h, row) {

                    return row.license_date ? row.license_date : '---'
                },

                latest_callback_date_time(h, row) {

                    return row.latest_callback_date_time ? row.latest_callback_date_time : '---';
                },
            },

            pagination:{chunk:5,nav: 'fixed',edge:true},

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

    methods:{
        lang: lang,
        getData(){
            axios.get('api/admin/showLicenseCallbacks').
            then(res=>{
                this.loading = false;
                this.data = res.data.map(data => {
                    return data;
                });
            }).catch(err => {
                this.loading =false;
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

.VueTables .table-responsive > table th {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    min-width: 150px; /* Set a minimum width for the columns */
    max-width: 300px; /* Set a maximum width for the columns if needed */
}


.glyphicon-sort {
    margin-left: 178px;
    margin-top: -19px;
}
</style>

<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{ 'Latest Callbacks' }}</h3>
            </div>
            <div class="card-body" id="afl_products">
                <div class="datatable-container">
                    <v-client-table
                        v-if="data"
                        :columns="columns"
                        :data="data"
                        :options="options"
                        :key="counter"
                    ></v-client-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {errorHandler} from "../../helpers/responseHandler";
import axios from 'axios'

export default {
    name :'latest-callbacks',

    data(){

        return {

            data: [],

            columns:['callback_id','license_code','callback_ip','callback_date_time','callback_domain'],

            options : {},

            counter: 0,
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                callback_id: 'callback_id',

                callback_date_time: 'callback_date_time',

                callback_ip: 'callback_ip',

                license_code:   'license_code',

                callback_domain: 'callback_domain',
            },

            templates: {

                callback_id(h,row){
                    return row.callback_id ?row.callback_id : '----';
                },

                callback_date_time(h,row){
                    return row.callback_date_time ?row.callback_date_time : '----';

                },

                callback_ip(h,row){
                    return row.callback_ip ?row.callback_ip : '----';

                },

                license_code(h,row){
                    return row.license_code ?row.license_code : '----';

                },

                callback_domain(h,row){
                    return row.callback_domain ?row.callback_domain : '----';

                },
            },

            headings: {
                callback_domain: 'Domain',

                callback_id: 'Callback Id',

                callback_date_time: 'Date',

                license_code: 'License',

                callback_ip: 'IP'

            },
        }
    },

    methods:{
        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.afu_latest_callbacks; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false

                    errorHandler(err, 'latest-callbacks')
                });
        },
    },
};

</script>
<style>
.datatable-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto;
}
</style>

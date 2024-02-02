<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header callbacks">
                <h3 class="card-title ">{{'Latest Callbacks'}}</h3>

                <div class="card-tools">

                    <button type="button"  :disabled="loading" class="btn btn-tool" data-card-widget="refresh"
                            @click="getData()" v-tooltip="trans('refresh')">

                        <i class="fas fa-sync-alt" :class="loading ? 'fa-spin': ''"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="afl_products">
                <div class="datatable-container">
                    <v-client-table
                        v-if="data"
                        :columns="columns"
                        :data="data"
                        :options="options"
                        :key="counter"
                    >
                        <template v-slot:callback_status="props">

                            <span :style="{ color: props.row.callback_status ? 'green' : 'red' }">

                            {{ props.row.callback_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>
                    </v-client-table>
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

            columns:['callback_domain','callback_ip','callback_date_time','callback_status'],

            options : {},

            counter: 0,

            loading : false
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                callback_domain: 'callback_domain',

                callback_date_time: 'callback_date_time',

                callback_ip: 'callback_ip',

                callback_status:   'callback_status',

            },

            templates: {

                callback_domain(h,row){
                    return row.callback_domain ?row.callback_domain : '----';
                },

                callback_date_time(h,row){
                    return row.callback_date_time ?row.callback_date_time : '----';

                },

                callback_ip(h,row){
                    return row.callback_ip ?row.callback_ip : '----';

                },
            },

            headings: {
                callback_domain: 'Domain',

                callback_date_time: 'Date',

                callback_ip: 'IP',

                callback_status: 'Status'

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

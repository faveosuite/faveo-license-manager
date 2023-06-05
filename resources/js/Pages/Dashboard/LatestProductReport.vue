<template>

    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{lang('Latest Product Report')}}</h3>
</div>
                <div class="card-body" id="afl_products">

                    <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"></v-client-table>
                </div>
            </div>
        </div>

</template>

<script>
import axios from "axios";
import {errorHandler} from "../../helpers/responseHandler";

export default {
    name :'latest-product-report',

    data(){

        return {

            data: [],

            columns:['report','date','status'],

            options : {},

            counter: 0
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                report: 'report',

                date: 'date',

                status:   'status',
            },

            templates: {

                report(h,row){
                    return row.report ?row.report : '----';
                },

                date(h,row){
                    return row.date ?row.date : '----';

                },

            },

            headings: {

                report: 'Report',

                date: 'Date',

              status: 'Status',

            },
        }
    },

    methods:{

        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.latest_product_reports; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false

                    errorHandler(err, 'latest-product-report')
                });
        },
    },
};

</script>

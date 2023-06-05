<template>

    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{('Latest Callbacks')}}</h3>
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
    name :'latest-callbacks',

    data(){

        return {

            data: [],

            columns:['version','type','ip','date','status'],

            options : {},

            counter: 0,
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version: 'version',

                date: 'date',

                type: 'type',

                ip: 'ip',

                status:   'status',
            },

            templates: {

                version(h,row){
                    return row.version ?row.version : '----';
                },

                date(h,row){
                    return row.date ?row.date : '----';

                },

                ip(h,row){
                    return row.ip ?row.ip : '----';

                },

            },

            headings: {

                version: 'Version',

                date: 'Date',

                type: 'Type',

                ip: 'IP'

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

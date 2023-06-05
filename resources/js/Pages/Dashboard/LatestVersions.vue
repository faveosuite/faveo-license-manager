<template>
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{lang('Latest Version')}}</h3>
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
    name :'latest-version',

    data(){

        return {

            data: [],

            columns:['version','date','expiration','status'],

            // options : {},

            counter: 0,
        };
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version: 'version',

                date: 'date',

                expiration: 'expiration',

                status:   'status',
            },

            templates: {

                version(h,row){
                    return row.version ?row.version : '----';
                },

                date(h,row){
                    return row.date ?row.date : '----';

                },

                expiration(h,row){
                    return row.expiration ?row.expiration : '----';

                },

            },

            headings: {

                version: 'Version',

                date: 'Date',

                expiration: 'Expiration',

            },
        }
    },

    methods:{

        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.latest_versions; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false

                    errorHandler(err, 'latest-versions')
                });
        },
    },
};

</script>
<style>
.VueTables__search-field{
    display : none;
}
.VuePagination{
    display : none;
}
</style>


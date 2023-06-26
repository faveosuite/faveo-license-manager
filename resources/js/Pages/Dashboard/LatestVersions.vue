<template>
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">Latest Version</h3>
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
import axios from "axios";
import {errorHandler} from "../../helpers/responseHandler";

export default {
    name :'latest-version',

    data(){

        return {

            data: [],

            columns:['version_id','version_date','version_expire_date','status'],

            // options : {},

            counter: 0,
        };
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version_id: 'version_id',

                version_date: 'version_date',

                version_expire_date: 'version_expire_date',

                status:   'status',
            },

            templates: {

                version_id(h,row){
                    return row.version_id ?row.version_id : '----';
                },

                version_date(h,row){
                    return row.version_date ?row.version_date : '----';

                },

                version_expire_date(h,row){
                    return row.version_expire_date ?row.version_expire_date : '----';

                },

            },

            headings: {

                version_id: 'Version',

                version_date: 'Date',

                version_expire_date: 'Expiration',

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
                .catch((err) => {
                    this.loading = false;
                    errorHandler(err, 'latest-versions');
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
 .datatable-container {
     max-height: 300px; /* Adjust the maximum height as per your needs */
     overflow-y: auto;
 }

</style>


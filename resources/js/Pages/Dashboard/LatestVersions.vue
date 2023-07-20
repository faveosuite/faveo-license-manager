<template>
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header versions">
                <h3 class="card-title">{{'Latest Versions'}}</h3>
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
                            <template v-slot:version_status="props">

                        <span :class="props.row.version_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.version_status ? 'Active' : 'Inactive'}}
                        </span>
                            </template>
                        </v-client-table>
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

            columns:['version_number','version_date','version_expire_date','version_status'],

             options : {},

            counter: 0,
        };
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version_number: 'version_number',

                version_date: 'version_date',

                version_expire_date: 'version_expire_date',

                version_status:    'version_status',
            },

            templates: {

                version_number(h,row){
                    return row.version_number ?row.version_number : '----';
                },

                version_date(h,row){
                    return row.version_date ?row.version_date : '----';

                },

                version_expire_date(h,row){
                    return row.version_expire_date ?row.version_expire_date : '----';

                },

            },

            headings: {

                version_date: 'Version Date',

                version_expire_date: 'Version Expire date',

                version_number: "Version",

                version_status: "Status"

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
    lang(key) {
        return `Mock translated value for ${key}`;
    },
};

</script>
<style>

 .datatable-container {
     max-height: 250px; /* Adjust the maximum height as per your needs */
     overflow-y: auto;
 }
.VueTables .table-responsive {
    display: block;
    width: 100%;
    position: inherit;
    overflow-x :hidden;

}
 .versions {
     background-color: transparent;
     border-bottom: 1px solid rgba(0,0,0,.125);
     padding: 0.75rem 1.25rem;
     position: relative;
     border-top-left-radius: 0.25rem;
     border-top: 3px solid rgba(0,0,0,.125);
     border-top-right-radius: 0.25rem;
     border-top-color: #28a745;
 }

</style>


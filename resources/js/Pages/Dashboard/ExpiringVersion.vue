<template>

    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{('Expiring Version')}}</h3>
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
    name :'expiring-version',

    data(){

        return {

            data: [],

            columns:['version_id','version_date','version_expire_date','version_number'],

            options : {},

            counter: 0
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version_id: 'version',

                version_date: 'version_date',

                version_expire_date: 'version_expire_date',

                version_number: 'version_number'
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
                version_number(h,row){
                    return row.version_number ?row.version_number : '----';

                },
            },

            headings: {

                version_id: 'Version',

                version_date: 'Version Date',

                version_expire_date: 'Version Expire date',

                version_number: "Version No."
            },
        }
    },

    methods:{

        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.expired_versions; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false

                    errorHandler(err, 'expiring-version')
                });
        },
    },
};

</script>

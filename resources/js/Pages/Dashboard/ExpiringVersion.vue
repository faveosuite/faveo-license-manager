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

export default {
    name :'expiring-version',

    data(){

        return {

            data: [],

            columns:['version','expiration_date'],

            options : {},

            counter: 0
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version: 'version',

                expiration_date: 'expiration_date',

            },

            templates: {

                version(h,row){
                    return row.version ?row.version : '----';
                },

                expiration_date(h,row){
                    return row.expiration_date ?row.expiration_date : '----';

                },

            },

            headings: {

                version: 'Version',

                expiration_date: 'Expiration Date',

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
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};

</script>

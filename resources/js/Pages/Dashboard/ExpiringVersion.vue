<template>
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header versions">
                <h3 class="card-title">{{'Expiring Version'}}</h3>
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

                            <span :style="{ color: props.row.version_status ? 'green' : 'red' }">

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
    name :'expiring-version',

    data(){

        return {

            data: [],

            columns:['version_number','version_date','version_expire_date','version_status'],

            options : {},

            counter: 0
        }
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                version_number: 'version_number',

                version_date: 'version_date',

                version_expire_date: 'version_expire_date',

                version_status: 'version_status'
            },

            templates: {

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

                version_number: 'Version',

                version_date: 'Version Date',

                version_expire_date: 'Version Expire date',

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
<style>
#afl_products .VueTables__limit {
display :none;
}

.datatable-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto;
}
</style>


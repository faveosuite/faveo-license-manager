<template>
    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header versions">
                <h3 class="card-title">{{'Latest Versions'}}</h3>
                <div class="card-tools">

                    <button type="button"  :disabled="loading" class="btn btn-tool" data-card-widget="refresh"
                            @click="getData()" v-tooltip="lang('refresh')">

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
import {lang} from "../../helpers/extraLogics";


export default {
    name :'latest-version',

    data(){

        return {

            columns:['version_number','version_date','version_expire_date','version_status'],

             options : {},

            counter: 0,

            loading: false

        };
    },

    beforeMount(){

        const self =this;

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

        lang: lang,

        getData() {

            this.$emit('refresh')
        },
    },

    props : {

        data : {type : Array, default : ()=>{}}
    }

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
</style>


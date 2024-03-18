<template>

        <div class="card card-light">
            <div class="card-header versions">
                <h3 class="card-title">{{'Expiring Version'}}</h3>

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

                        <template v-slot:version_number="props">

                            <router-link v-if="props.row.version_number" :to="'/versions/'+props.row.version_id+'/view'">{{ props.row.version_number }}</router-link>
                            <span v-else>----</span>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>
</template>


<script>

import moment from "moment";
import 'moment-timezone'
import {lang, formatDateTime} from "../../helpers/extraLogics";

export default {
    name :'expiring-version',

    data(){

        return {

            columns:['version_number','version_date','version_expire_date','version_status'],

            options : {},

            counter: 0,

            loading : false
        }
    },



    beforeMount(){

        const self =this;

        const date_format = this.generalSetting.date_format.js_format
        const time_format = this.generalSetting.time_format.js_format
        const timezone = this.generalSetting.timezone.name

        this.options ={

            columnsClasses:{

                version_number: 'version_number',

                version_date: 'version_date',

                version_expire_date: 'version_expire_date',

                version_status: 'version_status'
            },

            templates: {

                version_date(h,row){

                    return row.version_date ? moment(row.version_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                },

                version_expire_date(h,row){

                    return row.version_expire_date ? moment(row.version_expire_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
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

        lang: lang,

        getData() {

            this.$emit('refresh')
        },
    },

    props : {

        data : {type : Array, default : ()=>{}},

        generalSetting : {type : Object, default : () => {}},
    }
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


<template>

        <div class="card card-light">
            <div class="card-header callbacks">
                <h3 class="card-title ">{{'Latest Callbacks'}}</h3>

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
                        <template v-slot:callback_status="props">

                            <span :style="{ color: props.row.callback_status ? 'green' : 'red' }">

                            {{ props.row.callback_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>

                        <template v-slot:callback_domain="props">

                            <a v-if="props.row.callback_domain" :href="'https://'+props.row.callback_domain" target="_blank">{{props.row.callback_domain}}</a>

                            <span v-else>----</span>

                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>
</template>

<script>

import {lang, formatDateTime} from "../../helpers/extraLogics";

export default {
    name :'latest-callbacks',

    data(){

        return {

            columns:['callback_domain','callback_ip','callback_date_time','callback_status'],

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

                callback_domain: 'callback_domain',

                callback_date_time: 'callback_date_time',

                callback_ip: 'callback_ip',

                callback_status:   'callback_status',

            },

            templates: {

                callback_date_time(h,row){

                    return formatDateTime(row.callback_date_time, timezone, date_format, time_format)
                },

                callback_ip(h,row){
                    return row.callback_ip ?row.callback_ip : '----';
                },
            },

            headings: {

                callback_domain: 'Domain',

                callback_date_time: 'Date',

                callback_ip: 'IP',

                callback_status: 'Status'

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
.datatable-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto;
}
</style>

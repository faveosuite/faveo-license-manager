<template>

        <div class="card card-light">
            <div class="card-header callbacks">
                <h3 class="card-title ">{{'Latest Clients'}}</h3>

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
                        <template v-slot:client_status="props">

                            <span :style="{ color: props.row.client_status ? 'green' : 'red' }">

                            {{ props.row.client_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>

                        <template v-slot:client_email="props">

                            <router-link v-if="props.row.client_email" :to="'/clients/' + props.row.client_id + '/view'">{{ props.row.client_email }}</router-link>

                            <span v-else>----</span>
                        </template>

                        <template v-slot:full_name="props">

                            <router-link v-if="props.row.full_name" :to="'/clients/' + props.row.client_id + '/view'">{{ props.row.full_name }}</router-link>

                            <span v-else>----</span>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>
</template>

<script>

import {lang} from "../../helpers/extraLogics";
import moment from "moment";
import 'moment-timezone'

export default {
    name :'latest-clients',

    data(){

        return {

            columns:['full_name','client_email','client_active_date','license_count', 'client_status'],

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

                full_name: 'full_name',

                client_email: 'client_email',

                client_active_date: 'client_active_date',

                license_count: 'license_count',

                client_status:   'client_status',

            },

            templates: {

                client_active_date(h,row){
                    return row.client_active_date ? moment(row.client_active_date).tz(timezone).format(`${date_format} ${time_format}`) : '----'
                },

            },

            headings: {

                full_name: 'Name',

                client_email: 'Email',

                client_active_date: 'Activation Date',

                license_count: 'Licenses',

                client_status: 'Status'

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

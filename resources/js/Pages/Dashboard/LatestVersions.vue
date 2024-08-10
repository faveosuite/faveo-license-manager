<template>

        <div class="card card-light">
            <div class="card-header versions">
                <h3 class="card-title">{{lang('latest_versions')}}</h3>
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

                            {{ props.row.version_status ? lang('active') : lang('inactive')}}
                        </span>
                            </template>

                            <template v-slot:version_number="props">

                                <router-link :to="'/versions/'+props.row.version_id+'/view'">{{ props.row.version_number }}</router-link>
                            </template>

                            <template v-slot:product="props">
                                <router-link v-if="props.row.product && Array.isArray(props.row.product) && props.row.product[0] && props.row.product[0].product_title" :to="'/products/'+props.row.product[0].product_id+'/view'">{{ props.row.product[0].product_title }}</router-link>
                                <span v-else>----</span>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
</template>

<script>

import {formatDateTime, lang} from "../../helpers/extraLogics";

export default {
    name :'latest-version',

    data(){

        return {

            columns:['product', 'version_number','version_date','version_upgrade_count','version_status'],

            options : {},

            counter: 0,

            loading: false

        };
    },

    beforeMount(){

        const self =this;

        const date_format = this.generalSetting.date_format.js_format
        const time_format = this.generalSetting.time_format.js_format
        const timezone = this.generalSetting.timezone.name

        this.options ={

            columnsClasses:{

                product: 'product_title',

                version_number: 'version_number',

                version_upgrade_count: 'version_upgrade_count',

                version_date: 'version_date',

                version_status:    'version_status',
            },

            templates: {

                version_date(h,row){

                    return formatDateTime(row.version_date, timezone, date_format, time_format)
                },

                version_expire_date(h,row){

                    return formatDateTime(row.version_expire_date, timezone, date_format, time_format)
                },

            },

            headings: {

                product: this.lang('product'),

                version_date: this.lang('release_date'),

                version_upgrade_count: this.lang('upgrades'),

                version_number: this.lang('version'),

                version_status: this.lang('status')

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

.VueTables .table-responsive {
    display: block;
    width: 100%;
    position: inherit;
    overflow-x :hidden;

}
</style>


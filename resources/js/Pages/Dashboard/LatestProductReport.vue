<template>

        <div class="card card-light">
            <div class="card-header products">
                <h3 class="card-title">{{'Latest Product Report'}}</h3>

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
                        <template v-slot:report_status="props">

                            <span :style="{ color: props.row.report_status ? 'green' : 'red' }">

                            {{ props.row.report_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>

                        <template v-slot:license="props">

                            <router-link v-if="props.row.license && props.row.license.license_id" :to="'/licenses/' + props.row.license.license_id + '/view'">{{ props.row.license.license_code.match(/.{1,4}/g).join('-')}}</router-link>

                            <span v-else>----</span>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>
</template>

<script>

import {formatDateTime, lang} from "../../helpers/extraLogics";
import moment from "moment";
import 'moment-timezone'
export default {
    name: 'latest-product-report',
    data() {
        return {
            columns: ['report_text', 'report_date_time','license', 'report_status'],
            options: {},
            counter: 0,
            loading: false
        }
    },
    beforeMount() {

        const date_format = this.generalSetting.date_format.js_format;
        const time_format = this.generalSetting.time_format.js_format;
        const timezone = this.generalSetting.timezone.name;

        this.options = {

            columnsClasses: {

                report_text: 'report_status',

                report_date_time: 'report_date_time',

                license: 'license_code',

                report_status: 'report_status'
            },

            templates: {

                report_date_time(h, row) {

                    return formatDateTime(row.report_date_time, timezone, date_format, time_format)
                },
            },
            headings: {
                report_text: 'Report',

                report_date_time: 'Date',

                license:  'License Code',

                report_status:  'Status'
            },
        }
    },
    methods: {
        lang: lang,

        getData() {

            this.$emit('refresh')
        },
    },

    props : {

        data : {type: Array, default : ()=>{}},

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

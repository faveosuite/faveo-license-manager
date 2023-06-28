<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{ 'Latest Product Report' }}</h3>
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

                        <span :class="props.row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.report_status ? 'Active' : 'Inactive'}}
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
import { errorHandler } from "../../helpers/responseHandler";

export default {
    name: 'latest-product-report',
    data() {
        return {
            data: [],
            columns: ['report_text', 'report_date_time','license_code', 'report_status'],
            options: {},
            counter: 0
        }
    },
    beforeMount() {
        this.getData();

        this.options = {
            columnsClasses: {
                report_text: 'report_status',

                report_date_time: 'report_date_time',

                license_code: 'license_code',

                report_status: 'report_status'
            },
            templates: {
                license_code(h, row) {
                    return row.license_code ? row.license_code : '----';
                },
                report_date_time(h, row) {
                    return row.report_date_time ? row.report_date_time : '----';
                },
            },
            headings: {
                report_text: 'Report',

                report_date_time: 'Date',

                license_code:  'License Code',

                report_status:  'Status'
            },
        }
    },
    methods: {
        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.latest_product_reports; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false;
                    errorHandler(err, 'latest-product-report');
                });
        },
    },
};
</script>
<style>
.datatable-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto;
}
</style>

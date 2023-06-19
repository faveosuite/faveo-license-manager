<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{ 'Latest Product Report' }}</h3>
            </div>
            <div class="card-body" id="afl_products">
                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"></v-client-table>
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
            columns: ['report_id', 'report_date_time', 'status'],
            options: {},
            counter: 0
        }
    },
    beforeMount() {
        this.getData();

        this.options = {
            columnsClasses: {
                report_id: 'report_id',
                report_date_time: 'report_date_time',
                report_status: 'report_status',
            },
            templates: {
                report_id(h, row) {
                    return row.report_id ? row.report_id : '----';
                },
                report_date_time(h, row) {
                    return row.report_date_time ? row.report_date_time : '----';
                },
            },
            headings: {
                report_id: 'Report',
                report_date_time: 'Date',
                report_status: 'Status',
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

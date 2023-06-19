<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">

            <div class="card-header">
                <h3 class="card-title">{{('Latest Installations')}}</h3>
            </div>
            <div class="card-body" id="afl_products">
                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"></v-client-table>
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import {errorHandler} from "../../helpers/responseHandler";

export default {
    name: 'latest-installations',

    data() {
        return {
            data: [], // Initialize as an empty array to hold the fetched data
            columns: ['installation_id', 'product_id','license_code', 'installation_date','installation_ip', 'installation_domain'],
            options: {},
            counter: 0,
        };
    },

    beforeMount() {
        const self = this;
        this.getData();
        this.options = {
            columnsClasses: {
                installation_id: 'installation_id',
                product_id:'product_id',
                installation_ip: 'installation_ip',
                installation_date: 'installation_date',
                installation_domain: 'installation_domain',
            },
            templates: {
                installation_id(h, row) {
                    return row.installation_id ? row.installation_id : '----';
                },
                product_id(h, row) {
                    return row.product_id ? row.product_id : '----';
                },
                installation_ip(h, row) {
                    return row.installation_ip ? row.installation_ip : '----';
                },
                installation_date(h, row) {
                    return row.installation_date ? row.installation_date : '----';
                },
                installation_domain(h, row) {
                    return row.installation_domain ? row.installation_domain : '----';
                },
            },
            headings: {
                installation_id: 'Id',
                product_id:'Product Id',
                installation_date: 'Date',
                installation_ip: 'IP',
                installation_domain: 'Domain',
            },
        };
    },

    methods: {
        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.afl_latest_installation; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false

                    errorHandler(err, 'latest-installations')
                });
        },
    },
};
</script>

<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">

            <div class="card-header licences">

                <h3 class="card-title">{{'Latest Installations'}}</h3>

                <div class="card-tools">

                    <button type="button"  :disabled="loading" class="btn btn-tool" data-card-widget="refresh"
                            @click="getData()" v-tooltip="trans('refresh')">

                        <i class="fas fa-sync-alt" :class="loading ? 'fa-spin': ''"></i>
                    </button>
                </div>
            </div>
            <div class="card-body" id="afl_products">
                <div class="datatable-container my-table-container">
                    <v-client-table
                        v-if="data"
                        :columns="columns"
                        :data="data"
                        :options="options"
                        :key="counter"
                    >
                        <template v-slot:product_status="props">

                        <span :class="props.row.product_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.product_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>
                    </v-client-table>
                </div>
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
            columns: ['license_code','installation_ip','installation_date','installation_domain'],
            options: {},
            counter: 0,
            loading: false
        };
    },

    beforeMount() {
        const self = this;
        this.getData();
        this.options = {
            columnsClasses: {
                license_code: 'license_code',

                installation_ip: 'installation_ip',

                installation_date: 'installation_date',

                installation_domain: 'installation_domain',
            },
            templates: {
                license_code(h, row) {
                    const formattedLicenseCode = row.license_code ? row.license_code.match(/.{1,4}/g).join('-') : '----';
                    return formattedLicenseCode;
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
                license_code: 'License Code',
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
<style>
.my-table-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto !important;
}
.VueTables .table-responsive>table th {
    white-space: nowrap;
    width: 100px;
}
.glyphicon-sort {
    margin-left: 100px;
    margin-top: -19px;
}

</style>


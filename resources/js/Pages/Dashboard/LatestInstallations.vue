<template>
    <div class="col-sm-6 col-md-12 col-12">
        <div class="card card-light">

            <div class="card-header">
                <h3 class="card-title">{{lang('Latest Installations')}}</h3>
            </div>
            <div class="card-body" id="afl_products">
                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"></v-client-table>
            </div>
        </div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'latest-installations',

    data() {
        return {
            data: [], // Initialize as an empty array to hold the fetched data
            columns: ['version', 'ip', 'date', 'status'],
            options: {},
            counter: 0,
        };
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {
        const self = this;
        this.getData();
        this.options = {
            columnsClasses: {
                version: 'version',
                ip: 'ip',
                date: 'date',
                status: 'status',
            },
            templates: {
                version(h, row) {
                    return row.version ? row.version : '----';
                },
                date(h, row) {
                    return row.date ? row.date : '----';
                },
                ip(h, row) {
                    return row.ip ? row.ip : '----';
                },
            },
            headings: {
                version: 'Version',
                date: 'Date',
                ip: 'IP',
                status: 'Status',
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
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>

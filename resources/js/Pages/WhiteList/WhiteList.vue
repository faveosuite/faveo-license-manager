<template>

    <div class="col-sm-12">


        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_whitelist_ip')}}</h3>

                <div class="card-tools">

                    <router-link to="/whitelist/create" class="btn-tool" v-tooltip="lang('create_whitelist_ip')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="white_list">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:actions="props">

                        <table-actions :data="props.row"></table-actions>
                    </template>
                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

export default {

    name: 'whitelist',

    data() {

        return {

            data: '',

            columns: ['whitelist_host_ip', 'whitelist_host_comments', 'whitelist_host_date','actions'],

            options: {},

            counter: 0
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    async beforeMount() {

        const self = this;

        await this.getData();

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses: {

                whitelist_host_ip: 'whitelist_host_ip',

                whitelist_host_comments: 'whitelist_host_comments',

                whitelist_host_date: 'whitelist_host_date	',
            },

            templates: {

                whitelist_host_ip(h, row) {

                    return row.whitelist_host_ip ? row.whitelist_host_ip : '---';
                },

                whitelist_host_comments(h, row) {

                    return row.whitelist_host_comments ? row.whitelist_host_comments : '---'
                },

                whitelist_host_date(h, row) {

                    return row.whitelist_host_date ? row.whitelist_host_date : '---';
                },
            },

            pagination: { chunk: 5, nav: 'fixed', edge: true },

            headings: {

                whitelist_host_ip: 'IP Address',

                whitelist_host_comments: 'Comments',

                whitelist_host_date: 'Date',

                actions: 'Actions'
            },
        }
    },

    methods: {

        updateData() {
            this.getData();
        },

        async getData() {

            return await axios.get('/api/admin/view-Whitelist').then(res => {
                console.log('trew',res)
                this.loading = false;

                this.data = res.data.data.map(row => {
                    row.id = row.whitelist_host_id;
                    row.edit_url = '/whitelist/' + row.whitelist_host_id  + '/edit';
                    row.delete_url = `/api/admin/delete-whitelist-ip`;
                    row.keyVal = 'whitelist_host_id';
                    row.idVal = row.whitelist_host_id;
                    return row
                })
            }).catch(err => {

                this.loading = false;
            })
        }
    }
};
</script>

<style>
 .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

.VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>

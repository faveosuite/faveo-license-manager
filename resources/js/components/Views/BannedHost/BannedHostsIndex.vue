<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View existing banned hosts. If any banned host needs to be modified, click the IP address. If any banned
                host needs to be deleted, check the IP address and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_banned_hosts')}}</h3>

                <div class="card-tools">

                    <router-link to="/banned-hosts/create" class="btn-tool" v-tooltip="lang('create_banned_host')">

                        <i class="fas fa-plus"></i>
                    </router-link>
                </div>
            </div>

            <div class="card-body" id="banned_hosts">

                <v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

                </v-client-table>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';

    export default {

        name: 'banned-hosts',

        data() {

            return {

                data: '',

                columns: ['banned_host_ip', 'banned_host_comments', 'banned_host_date', 'banned_host_blocks', 'banned_host_last_block_date', 'actions'],

                options: {},

                counter: 0
            }
        },

        created() {

            window.eventHub.$on('refreshData', this.updateData);
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

                    banned_host_ip: 'banned_host_ip',

                    banned_host_comments: 'banned_host_comments',

                    banned_host_date: 'banned_host_date	',

                    banned_host_blocks: 'banned_host_blocks',

                    banned_host_last_block_date: 'banned_host_last_block_date',
                },

                templates: {

                    banned_host_ip(h, row) {

                        return row.banned_host_ip ? row.banned_host_ip : '---';
                    },

                    banned_host_comments(h, row) {

                        return row.banned_host_comments ? row.banned_host_comments : '---'
                    },

                    banned_host_date(h, row) {

                        return row.banned_host_date ? row.banned_host_date : '---';
                    },

                    banned_host_blocks(h, row) {

                        return row.banned_host_blocks ? row.banned_host_blocks : '---';
                    },

                    banned_host_last_block_date(h, row) {

                        return row.banned_host_last_block_date ? row.banned_host_last_block_date : '---';
                    },

                    status(createElement, row) {

                        let span = createElement('span', {

                            attrs: {
                                'class': row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                            }
                        }, row.status ? 'Active' : 'Inactive');

                        return createElement('a', {}, [span]);
                    },
                    actions: 'table-actions'
                },

                pagination: { chunk: 5, nav: 'fixed', edge: true },

                headings: {

                    banned_host_ip: 'IP Address',

                    banned_host_comments: 'Comments',

                    banned_host_date: 'Date',

                    banned_host_blocks: 'Blocks',

                    banned_host_last_block_date: 'Latest Blocks',

                    actions: 'Actions'
                },
            }
        },

        methods: {

            updateData() {
                this.getData();
            },

            async getData() {
                this.loading = true;

                return await axios.get('/api/admin/viewBannedHost').then(res => {

                    this.loading = false;

                    this.data = res.data.data.map(row => {
                        row.id = row.banned_host_id;
                        row.edit_url = '/banned-hosts/' + row.banned_host_id + '/edit';
                        row.delete_url = `/api/admin/bannedHosts/delete`;
                        row.keyVal = 'banned_host_id';
                        row.idVal = row.banned_host_id;
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
    #banned_hosts .VueTables .table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
    }

    #banned_hosts .VueTables .table-responsive>table {
        width: max-content;
        min-width: 100%;
        max-width: max-content;
        overflow: auto !important;
    }
</style>

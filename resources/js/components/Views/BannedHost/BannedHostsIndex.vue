<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>View existing banned hosts. If any banned host needs to be modified, click the IP address. If any banned host needs to be deleted, check the IP address and click the 'Submit' button.</p>
        </div>

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

    name : 'api-keys',

    data() {

        return {

            data : '',

            columns: ['ip_address', 'comments', 'date', 'blocks', 'latest_blocks'],

            options: {},

            counter : 0
        }
    },

    created() {

        window.eventHub.$on('refreshData',this.updateData);
    },

    beforeMount(){

        const self= this;

        this.getData();

        this.options = {

            sortIcon: {

                base : 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses : {

                ip_address : 'ip_address',

                comments: 'comments',

                date : 'date',

                blocks: 'blocks',

                latest_blocks : 'latest_blocks',
            },

            templates : {

                ip_address(h,row){

                    return row.ip_address ? row.ip_address : '---';
                },

                comments(h,row){

                    return row.comments ? row.comments : '---'
                },

                date(h,row) {

                    return row.date ? row.date : '---';
                },

                blocks(h,row) {

                    return row.blocks ? row.blocks : '---';
                },

                latest_blocks(h,row) {

                    return row.latest_blocks ? row.latest_blocks : '---';
                },

                status(createElement, row) {

                    let span = createElement('span', {

                        attrs: {
                            'class' : row.status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
                        }
                    }, row.status ? 'Active' : 'Inactive');

                    return createElement('a',{},[span]);
                },
            },

            pagination:{chunk:5,nav: 'fixed',edge:true},

            headings: {

                ip_address: 'IP Address',

                comments: 'Comments',

                date: 'Date',

                blocks: 'Blocks',

                latest_blocks : 'Latest Blocks',
            },
        }
    },

    methods : {

        updateData() {

            this.getData();
        },

        async getData() {

            await axios.get('/api/admin/viewBannedHost').then(res=>{

                this.data = res.data.data.map(row => {
                    return {
                        id: row.banned_host_id,
                        ip_address: row.banned_host_ip,
                        comments: row.banned_host_comments,
                        date: row.banned_host_date,
                        blocks: row.banned_host_blocks,
                        latest_blocks: row.banned_host_last_block_date,
                    };
                })
            })
        }
    }
};
</script>

<style>

#banned_hosts .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#banned_hosts .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

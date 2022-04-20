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

            <div class="card-body" id="my_licenses">

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

        getData() {

            axios.get('/api/admin/viewBannedHost').then(res=>{

                this.data = res.data.data.map(data => {

                    data.edit_url = '/bannedHosts/' + data.license_id + '/edit';

                    data.delete_url = '/api/admin/bannedHosts/delete';

                    return data;
                })
            })
        }
    }
};
</script>

<style>

.license_product_title,.license_code,.license_install,.license_callbacks, .latest_callback_time, .license_date{ max-width: 200px; word-break: break-all;}

#my_licenses .VueTables .table-responsive {
    overflow-x: auto;overflow-y: hidden;
}

#my_licenses .VueTables .table-responsive > table{
    width : max-content;
    min-width : 100%;
    max-width : max-content;
    overflow: auto !important;
}
</style>

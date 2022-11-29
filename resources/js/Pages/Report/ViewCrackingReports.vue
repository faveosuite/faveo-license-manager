<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="dataTableModal" />

        <div class="card card-light ">

            <div class="card-header">

                <h3 class="card-title">{{lang('view_cracking_reports')}}</h3>

            </div>

            <div class="card-body" id="my_licenses">

                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:product_title="props">

                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>
                    </template>

                    <template v-slot:license_status="props">

                        <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.license_status ? 'Active' : 'Inactive'}}
                        </span>
                    </template>

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

    name: 'licenses-list',

    data() {

        return {

            data: '',

            columns: ['product_id', 'report_id', 'license_code','report_date_time','report_text' ,'actions'],

            options: {},

            counter: 0
        }
    },

    created() {

        this.emitter.on('refreshData', this.updateData);
    },

    beforeMount() {

        const self = this;

        this.getData();

        this.options = {

            sortIcon: {

                base: 'glyphicon',

                up: 'glyphicon-chevron-up',

                down: 'glyphicon-chevron-down'
            },

            texts: { filter: '', limit: '' },

            columnsClasses: {

                product_id: 'license_product_title',

                report_id: 'report_id',

                license_code: 'license_code',

                account_id: 'account_id',

                report_date_time: 'report_date_time',

                report_text:  'report_text',
            },

            templates: {

                license_code(h, row) {

                    return row.license_code ? row.license_code : '---';
                },

                license_date(h, row) {

                    return row.license_date ? row.license_date : '---'
                },

                latest_callback_date_time(h, row) {

                    return row.latest_callback_date_time ? row.latest_callback_date_time : '---';
                },
            },

            pagination: { chunk: 5, nav: 'fixed', edge: true },

            headings: {

                product_id: 'Product',

                report_id:   'Report',

                license_code: 'License Code',

                report_text:  'Report',

                report_date_time: 'Report Date Time',

                actions: 'Actions'
            },
        }
    },

    methods: {

        updateData() {

            this.getData();
        },

        getData() {

            this.loading = true;

            axios.get('/api/admin/reportCracking').then(res => {

                this.loading = false;

                this.data = res.data.map(data => {

                    return data;
                })
            }).catch(err => {

                this.loading = false;
            })
        }
    }
};
</script>

<style>
.license_product_title,
.license_code,
.license_install,
.license_callbacks,
.latest_callback_time,
.license_date {
    max-width: 200px;
    word-break: break-all;
}

#my_licenses .VueTables .table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
}

#my_licenses .VueTables .table-responsive>table {
    width: max-content;
    min-width: 100%;
    max-width: max-content;
    overflow: auto !important;
}
</style>


<!--<template>-->

<!--    <div class="col-sm-12">-->

<!--                <div class="alert alert-info">-->
<!--                    <span>View cracking reports. If any report needs to be deleted, check the report and click the 'Submit' button.<br><br>-->
<!--                        <b>Attention</b>: all failed installations and verifications are displayed in View License Reports section; therefore, this section should always be empty. If you see any record here, most likely someone was sending invalid data to licensing server. If automatic hosts banning is enabled, Auto PHP Licenser will ban attacker automatically. Otherwise, you should ban attacker's host manually.</span>-->
<!--                </div>-->

<!--        <div class="row" v-if="loading">-->

<!--            <custom-loader :duration="4000"></custom-loader>-->
<!--        </div>-->

<!--        <alert componentName="dataTableModal" />-->

<!--        <div class="card card-light ">-->

<!--            <div class="card-header">-->

<!--                <h3 class="card-title">{{lang('view_cracking_reports')}}</h3>-->
<!--            </div>-->

<!--            <div class="card-body" id="view_cracking_reports">-->

<!--                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">-->


<!--                    <template v-slot:product_title="props">-->

<!--                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>-->
<!--                    </template>-->

<!--                    <template v-slot:license_status="props">-->

<!--                        <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">-->

<!--                            {{ props.row.license_status ? 'Active' : 'Inactive'}}-->
<!--                        </span>-->
<!--                    </template>-->

<!--                    <template v-slot:actions="props">-->

<!--                        <table-actions :data="props.row"></table-actions>-->
<!--                    </template>-->

<!--                </v-client-table>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->

<!--<script>-->

<!--import axios from 'axios';-->

<!--export default {-->

<!--    name:'cracking_reports',-->

<!--    data() {-->

<!--        return {-->

<!--            data: '',-->

<!--            columns: ['product_title', 'license_code', 'total_installations', 'total_callbacks', 'license_date',-->
<!--                'latest_callback_date_time', 'actions'],-->
<!--            options: {},-->

<!--            counter: 0-->
<!--        }-->
<!--    },-->

<!--    created() {-->

<!--        this.emitter.on('refreshData', this.updateData);-->
<!--    },-->

<!--    async beforeMount() {-->

<!--        const self = this;-->

<!--        await this.getData();-->

<!--        this.options = {-->

<!--            sortIcon: {-->

<!--                base: 'glyphicon',-->

<!--                up: 'glyphicon-chevron-up',-->

<!--                down: 'glyphicon-chevron-down'-->
<!--            },-->

<!--            texts: { filter: '', limit: '' },-->

<!--                     columnsClasses : {-->

<!--                         product_title: 'license_product_title',-->

<!--                         license_code: 'license_code',-->

<!--                         total_installations: 'license_install',-->

<!--                         total_callbacks: 'license_callbacks',-->

<!--                         latest_callback_date_time: 'latest_callback_time',-->

<!--                         license_date: 'license_date',-->
<!--                     },-->

<!--            templates : {-->

<!--                product_title(createElement, row) {-->

<!--                    if(row.product_id) {-->

<!--                        return createElement('router-link', {-->
<!--                            attrs: {-->
<!--                                to: '/products/'+row.product_id+'/edit'-->
<!--                            }-->
<!--                        }, row.product_title);-->

<!--                    } else{-->
<!--                        return '-&#45;&#45;'-->
<!--                    }-->
<!--                },-->

<!--                latest_report(h,row){-->

<!--                    return row.client_license ? row.client_license.report_date : '-&#45;&#45;';-->
<!--                },-->

<!--                report_status(createElement, row) {-->

<!--                    let span = createElement('span', {-->

<!--                        attrs: {-->
<!--                            'class' : row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'-->
<!--                        }-->
<!--                    }, row.report_status ? 'Active' : 'Inactive');-->

<!--                    return createElement('a',{},[span]);-->
<!--                },-->

<!--                actions : 'table-actions'-->
<!--            },-->

<!--            pagination: { chunk: 5, nav: 'fixed', edge: true },-->

<!--                     headings: {-->
<!--                         product_id: 'Product',-->

<!--                         license_code: 'License Code',-->

<!--                         total_installations: 'Installations',-->

<!--                         total_callbacks: 'Callbacks',-->

<!--                         latest_callback_date_time: 'Latest Callback',-->

<!--                         license_date: 'Latest License',-->

<!--                         actions: 'Actions'-->
<!--                  },-->
<!--        }-->
<!--    },-->

<!--    methods: {-->

<!--        updateData() {-->
<!--            this.getData();-->
<!--        },-->

<!--        getData(){-->
<!--            this.loaading =true;-->
<!--            axios.get('api/admin/viewLicenses').then(res => {-->
<!--                this.loading =false;-->
<!--                console.log(res.data);-->
<!--                this.data =res.data.data.map(data =>{-->

<!--                    data.edit_url = '/licenses/' + data.license_id + '/edit';-->

<!--                    data.delete_url = '/api/admin/license/delete';-->

<!--                    data.keyVal = 'license_id';-->

<!--                    data.idVal = data.license_id;-->

<!--                    console.log(data);-->

<!--                    return data;-->
<!--                })-->
<!--            })-->
<!--        }-->

<!--    }-->
<!--};-->
<!--</script>-->

<!--<style>-->
<!--#banned_hosts .VueTables .table-responsive {-->
<!--    overflow-x: auto;-->
<!--    overflow-y: hidden;-->
<!--}-->

<!--#banned_hosts .VueTables .table-responsive>table {-->
<!--    width: max-content;-->
<!--    min-width: 100%;-->
<!--    max-width: max-content;-->
<!--    overflow: auto !important;-->
<!--}-->
<!--</style>-->


<!--&lt;!&ndash;<template>&ndash;&gt;-->

<!--&lt;!&ndash;    <div class="col-sm-12">&ndash;&gt;-->

<!--&lt;!&ndash;        <div class="alert alert-info">&ndash;&gt;-->
<!--&lt;!&ndash;            <span>View cracking reports. If any report needs to be deleted, check the report and click the 'Submit' button.<br><br>&ndash;&gt;-->
<!--&lt;!&ndash;                <b>Attention</b>: all failed installations and verifications are displayed in View License Reports section; therefore, this section should always be empty. If you see any record here, most likely someone was sending invalid data to licensing server. If automatic hosts banning is enabled, Auto PHP Licenser will ban attacker automatically. Otherwise, you should ban attacker's host manually.</span>&ndash;&gt;-->
<!--&lt;!&ndash;        </div>&ndash;&gt;-->

<!--&lt;!&ndash;        <div class="row" v-if="loading">&ndash;&gt;-->

<!--&lt;!&ndash;            <custom-loader :duration="4000"></custom-loader>&ndash;&gt;-->
<!--&lt;!&ndash;        </div>&ndash;&gt;-->

<!--&lt;!&ndash;        <alert componentName="dataTableModal"/>&ndash;&gt;-->

<!--&lt;!&ndash;        <div class="card card-light ">&ndash;&gt;-->

<!--&lt;!&ndash;            <div class="card-header">&ndash;&gt;-->

<!--&lt;!&ndash;                <h3 class="card-title">{{lang('view_cracking_reports')}}</h3>&ndash;&gt;-->

<!--&lt;!&ndash;            </div>&ndash;&gt;-->

<!--&lt;!&ndash;            <div class="card-body" id="my_cracking">&ndash;&gt;-->

<!--&lt;!&ndash;                <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">&ndash;&gt;-->

<!--&lt;!&ndash;                </v-client-table>&ndash;&gt;-->
<!--&lt;!&ndash;            </div>&ndash;&gt;-->
<!--&lt;!&ndash;        </div>&ndash;&gt;-->
<!--&lt;!&ndash;    </div>&ndash;&gt;-->
<!--&lt;!&ndash;</template>&ndash;&gt;-->

<!--&lt;!&ndash;<script>&ndash;&gt;-->

<!--&lt;!&ndash;import axios from 'axios';&ndash;&gt;-->

<!--&lt;!&ndash;export default {&ndash;&gt;-->

<!--&lt;!&ndash;    name : 'cracking-list',&ndash;&gt;-->

<!--&lt;!&ndash;    data() {&ndash;&gt;-->

<!--&lt;!&ndash;        return {&ndash;&gt;-->

<!--&lt;!&ndash;            data : '',&ndash;&gt;-->

<!--&lt;!&ndash;            columns: ['report_title', 'client_license', 'report_date', 'report_status'],&ndash;&gt;-->

<!--&lt;!&ndash;            options: {},&ndash;&gt;-->

<!--&lt;!&ndash;            counter : 0,&ndash;&gt;-->

<!--&lt;!&ndash;            report_id : '',&ndash;&gt;-->

<!--&lt;!&ndash;            loading : false&ndash;&gt;-->
<!--&lt;!&ndash;        }&ndash;&gt;-->
<!--&lt;!&ndash;    },&ndash;&gt;-->

<!--&lt;!&ndash;    created() {&ndash;&gt;-->

<!--&lt;!&ndash;        this.emitter.on('refreshData',this.updateData);&ndash;&gt;-->
<!--&lt;!&ndash;    },&ndash;&gt;-->

<!--&lt;!&ndash;    beforeMount(){&ndash;&gt;-->

<!--&lt;!&ndash;        const self= this;&ndash;&gt;-->

<!--&lt;!&ndash;        // this.getData();&ndash;&gt;-->

<!--&lt;!&ndash;        this.options = {&ndash;&gt;-->

<!--&lt;!&ndash;            sortIcon: {&ndash;&gt;-->

<!--&lt;!&ndash;                base : 'glyphicon',&ndash;&gt;-->

<!--&lt;!&ndash;                up: 'glyphicon-chevron-up',&ndash;&gt;-->

<!--&lt;!&ndash;                down: 'glyphicon-chevron-down'&ndash;&gt;-->
<!--&lt;!&ndash;            },&ndash;&gt;-->

<!--&lt;!&ndash;            texts: { filter: '', limit: '' },&ndash;&gt;-->

<!--&lt;!&ndash;            columnsClasses : {&ndash;&gt;-->

<!--&lt;!&ndash;                report_title : 'i_report_title',&ndash;&gt;-->

<!--&lt;!&ndash;                client_license : 'i_client_license',&ndash;&gt;-->

<!--&lt;!&ndash;                report_date : 'i_report_date',&ndash;&gt;-->

<!--&lt;!&ndash;                report_status : 'i_report_status',&ndash;&gt;-->
<!--&lt;!&ndash;            },&ndash;&gt;-->

<!--&lt;!&ndash;            templates : {&ndash;&gt;-->

<!--&lt;!&ndash;                product_title(createElement, row) {&ndash;&gt;-->

<!--&lt;!&ndash;                    if(row.product_id) {&ndash;&gt;-->

<!--&lt;!&ndash;                        return createElement('router-link', {&ndash;&gt;-->
<!--&lt;!&ndash;                            attrs: {&ndash;&gt;-->
<!--&lt;!&ndash;                                to: '/products/'+row.product_id+'/edit'&ndash;&gt;-->
<!--&lt;!&ndash;                            }&ndash;&gt;-->
<!--&lt;!&ndash;                        }, row.product_title);&ndash;&gt;-->

<!--&lt;!&ndash;                    } else{&ndash;&gt;-->
<!--&lt;!&ndash;                        return '-&#45;&#45;'&ndash;&gt;-->
<!--&lt;!&ndash;                    }&ndash;&gt;-->
<!--&lt;!&ndash;                },&ndash;&gt;-->

<!--&lt;!&ndash;                latest_report(h,row){&ndash;&gt;-->

<!--&lt;!&ndash;                    return row.client_license ? row.client_license.report_date : '-&#45;&#45;';&ndash;&gt;-->
<!--&lt;!&ndash;                },&ndash;&gt;-->

<!--&lt;!&ndash;                report_status(createElement, row) {&ndash;&gt;-->

<!--&lt;!&ndash;                    let span = createElement('span', {&ndash;&gt;-->

<!--&lt;!&ndash;                        attrs: {&ndash;&gt;-->
<!--&lt;!&ndash;                            'class' : row.report_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'&ndash;&gt;-->
<!--&lt;!&ndash;                        }&ndash;&gt;-->
<!--&lt;!&ndash;                    }, row.report_status ? 'Active' : 'Inactive');&ndash;&gt;-->

<!--&lt;!&ndash;                    return createElement('a',{},[span]);&ndash;&gt;-->
<!--&lt;!&ndash;                },&ndash;&gt;-->

<!--&lt;!&ndash;                actions : 'table-actions'&ndash;&gt;-->
<!--&lt;!&ndash;            },&ndash;&gt;-->

<!--&lt;!&ndash;            pagination:{chunk:5,nav: 'fixed',edge:true},&ndash;&gt;-->

<!--&lt;!&ndash;            headings: {&ndash;&gt;-->

<!--&lt;!&ndash;                report_title : 'Report',&ndash;&gt;-->

<!--&lt;!&ndash;                client_license : 'Client or License Code',&ndash;&gt;-->

<!--&lt;!&ndash;                report_date : 'Date',&ndash;&gt;-->

<!--&lt;!&ndash;                report_status : 'Status',&ndash;&gt;-->
<!--&lt;!&ndash;            },&ndash;&gt;-->
<!--&lt;!&ndash;        }&ndash;&gt;-->
<!--&lt;!&ndash;    },&ndash;&gt;-->


<!--&lt;!&ndash;};&ndash;&gt;-->
<!--&lt;!&ndash;</script>&ndash;&gt;-->

<!--&lt;!&ndash;<style>&ndash;&gt;-->

<!--&lt;!&ndash;.i_product_title,.i_license_code,.i_total_installations,.i_latest_installation,.i_installation_status{ max-width: 200px; word-break: break-all;}&ndash;&gt;-->

<!--&lt;!&ndash;#my_cracking .VueTables .table-responsive {&ndash;&gt;-->
<!--&lt;!&ndash;    overflow-x: auto;overflow-y: hidden;&ndash;&gt;-->
<!--&lt;!&ndash;}&ndash;&gt;-->

<!--&lt;!&ndash;#my_cracking .VueTables .table-responsive > table{&ndash;&gt;-->
<!--&lt;!&ndash;    width : max-content;&ndash;&gt;-->
<!--&lt;!&ndash;    min-width : 100%;&ndash;&gt;-->
<!--&lt;!&ndash;    max-width : max-content;&ndash;&gt;-->
<!--&lt;!&ndash;    overflow: auto !important;&ndash;&gt;-->
<!--&lt;!&ndash;}&ndash;&gt;-->
<!--&lt;!&ndash;</style>&ndash;&gt;-->

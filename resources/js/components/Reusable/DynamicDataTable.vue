<template>
    <div class="datatable">

        <div v-if="showTable" class="row float-right mr-0 mb-3">

            <div v-if="option.filterable">

                <input type="text" class="form-control globe-search" v-model="search_str"
                       @keyup.enter="checkFile()" :style="inputStyle" :placeholder="trans('type_and_enter_to_search')">
            </div>
        </div>

        <v-server-table v-if="showTable" ref="table" :onLimit="onLimitChange" :url="endPoint" :columns="columnArray" :options="optionsObject" @error="onError" @loaded="onLoaded" :key="counter">

            <template v-slot:product_url_homepage="props">

                <a v-if="props.row.product_url_homepage" :href="props.row.product_url_homepage" target="_blank">{{props.row.product_url_homepage}}</a>

                <span v-else>&#45;&#45;</span>
            </template>

            <template v-slot:order_number="props">
                <a :href="extractHref(props.row.order_url)" target="_blank">
                    {{ props.row.order_url.match(/\d+/)[0] ?? '---' }}
                </a>
            </template>



            <template v-slot:license_status="props">

                <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                    {{ props.row.license_status ? 'Active' : 'Inactive'}}
                </span>
            </template>

            <template v-slot:product_status="props">

                <span :class="props.row.product_status ? 'text-green' : 'text-red'">

                    {{ props.row.product_status ? 'Active' : 'Inactive'}}
                </span>
            </template>

            <template v-slot:client_status="props">

                <span :style="{ color: props.row.client_status ? 'green' : 'red' }">

                    {{ props.row.client_status ? 'Active' : 'Inactive' }}
                </span>
            </template>

            <template v-slot:installation_status="props">

                <span :style="{ color: props.row.installation_status ? 'green' : 'red' }">

                    {{ props.row.installation_status ? 'Active' : 'Inactive' }}
                </span>
            </template>

            <template v-slot:api_key_licenses_add="props">

                {{ props.row.api_key_licenses_add ? 'Active' : 'Inactive'}}
                                        /
                {{ props.row.api_key_licenses_edit ? 'Active' : 'Inactive'}}

            </template>

            <template v-slot:api_key_clients_edit="props">

                {{ props.row.api_key_clients_add ? 'Active' : 'Inactive'}}
                                        /
                {{ props.row.api_key_clients_edit ? 'Active' : 'Inactive'}}
            </template>

            <template v-slot:api_key_products_add_edit="props">

                {{ props.row.api_key_products_add ? 'Active' : 'Inactive'}}
                                        /
                {{ props.row.api_key_products_edit ? 'Active' : 'Inactive'}}

            </template>

            <template v-slot:api_key_search="props">

                {{ props.row.api_key_search ? 'Active' : 'Inactive'}}

            </template>

            <template v-slot:api_key_status="props">

                {{ props.row.api_key_status ? 'Active' : 'Inactive'}}

            </template>

            <template v-slot:api_key_installations_edit="props">

                {{ props.row.api_key_installations_edit ? 'Active' : 'Inactive'}}

            </template>

            <template v-slot:report_status="props">

                <span :style="{ color: props.row.report_status ? 'green' : 'red' }">

                    {{ props.row.report_status ? 'Active' : 'Inactive'}}

                </span>

            </template>

            <template v-slot:full_name="props">

                <router-link :to="'/clients/' + props.row.client_id + '/edit'">{{ props.row.full_name }}</router-link>
            </template>

            <template v-slot:client_email="props">

                <router-link :to="'/clients/' + props.row.client_id + '/edit'">{{ props.row.client_email }}</router-link>
            </template>

            <template v-if="isLoading && !disableLoader" #afterTable>

                <custom-loader loaderType='clip-loader' :color="color"></custom-loader>
            </template>

            <template v-slot:product_title="props">

                <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>
            </template>

            <template v-slot:actions="props">

                <table-actions :data="props.row"></table-actions>
            </template>

        </v-server-table>

        <div v-if="!showTable && error_message" class="callout callout-danger bg-danger">

            <p><i class="fa fa-exclamation-triangle"> </i> {{(error_message)}}</p>
        </div>

        <div v-if="loading && !disableLoader" class="row faveo-datatable-loader">

            <loader :animation-duration="4000" :color="color" :size="60"/>
        </div>


        <div class="pagination-container">

            <div v-if="showTable && !loading && show_pagination">
                <template v-if="total == 1">
                    {{trans('one_record')}}
                </template>
                <template v-if="total > 1 && total <= 10">
                    {{ total }} {{trans('records')}}
                </template>
                <template v-if="total > 10">
                    {{trans('showing')}} {{ from }} to {{ to }} of {{ total }} {{trans('records')}}
                </template>
            </div>

            <div v-if="showTable && !loading && show_pagination && total > 10" class="float-right mr-0 pt-2">

                <simple-pagination :next_page="next_page" :prev_page="prev_page" :onPagination="onPagination">

                </simple-pagination>
            </div>
        </div>


    </div>
</template>

<script>

import { errorHandler } from '../../helpers/responseHandler';
import PageLoader from '../Reusable/Loader.vue';
import SimplePagination from "../Reusable/SimplePagination.vue";
// import {computed} from 'Vue'
import {EventBus} from "v-tables-3";
import {lang} from "../../helpers/extraLogics";

export default {

    name:'datatable',

    description:'Datatable that handles formatting queries in a way that it makes it easy to integrate with external APIs',

    props:{

        /**
         * Columns in the datatable.
         * Columns should atleast have title and field as
         * @return {Array}  columns in the datatable.(array of objects)
         */
        dataColumns: {type: Array, required: true},

        option:{type:Object},

        url:{type:String},

        tickets : {type:Function,default : ()=>[]},

        scroll_to : { type: String, default : ''},

        componentTitle : { type : String, default : ''},

        color : { type : String, default : '#1d78ff'},

        /**
         * Alert component name to dispatch alert box
         */
        alertComponentName: { type: String, default: '' },

        inputStyle: {type:Object, default :  ()=>{} },

        show_pagination : { type : Boolean, default : false },

        disableLoader : {type: Boolean, default: false}
    },

    data(){

        return{

            columnArray : this.dataColumns,

            optionsObj : this.option,

            endPoint : this.url,

            showTable : true,

            error_message : '',

            loading : false,

            markedRows : [],

            allMarked : false,

            styleObj : { display : 'none'},

            isLoading: false,

            counter : 0,

            search_str : '',

            next_page : '',

            prev_page : '',

            total: '',

            to: '',

            from: ''
        }
    },

    watch: {

        url(newValue,oldValue){

            this.endPoint = newValue
        },

        option(newValue,oldValue){

            this.optionsObj = newValue
        },

        dataColumns(newValue,oldValue){

            this.columnArray = newValue

            if(this.show_pagination){

                this.endPoint = this.url;
            }
        },

        markedRows(newValue,oldValue){

            this.tickets(this.markedRows)

            return newValue
        }
    },

    computed : {

        optionsObject() {

            const self = this;

            self.optionsObj.texts = {
                noResults: lang('no_matching_records'),
                loading: lang('loading')
            };

            if(self.optionsObj.headings && self.optionsObj.headings.hasOwnProperty('id')){

                self.optionsObj.headings.id = function(){

                    return self.h('input',{

                        type : 'checkbox',

                        modelValue : self.allMarked,

                        onChange(event) {

                            self.allMarked = event.target.checked;

                            self.toggleAll()
                        }
                    })
                }
            }

            this.optionsObj.debounce = 700;

            if(this.show_pagination) {

                this.optionsObj['pagination'] = { show : false };
            }

            return this.optionsObj
        }
    },

    methods :{
        lang,

        extractHref(orderUrl) {

            const parser = new DOMParser();
            // Parse the HTML string
            const parsedHtml = parser.parseFromString(orderUrl, 'text/html');
            // Get the root element of the parsed HTML
            const htmlElement = parsedHtml.documentElement;
            const tag = htmlElement.getElementsByTagName('a')

            return tag[0].getAttribute('href')
        },

        checkFile() {

            this.$refs.table.setFilter(this.search_str)
        },

        unmarkAll() {

            this.allMarked = false;
        },

        unselectAll() {

            this.allMarked = false;

            this.markedRows = [];
        },

        toggleAll() {

            this.markedRows = this.allMarked?this.$refs.table.data.map(row=>row.id):[];
        },

        onUpdate() {

            this.counter++;
        },

        onError(data){

            if(this.alertComponentName && data && data.response && data.response.status) {

                errorHandler(data, this.alertComponentName)

            } else {

                if(data && data.response) {

                    this.error_message = data.response.data.message;

                    this.onUpdate();
                }
            }

            if(data && data.response && data.response.data.message === 'Invalid API end-point'){

                this.$refs.table.refresh();

                this.showTable = true;

                this.loading = true;
            } else {

                this.showTable = true

                this.loading = false
            }
        },

        onLoaded(resp){

            if(this.show_pagination){

                this.next_page = resp.data.data.next_page_url;

                this.prev_page = resp.data.data.prev_page_url;

                this.total = resp.data.data.total;

                this.to = resp.data.data.to;

                this.from = resp.data.data.from;
            }

            this.loading = false

            this.styleObj.display = 'block'
        },

        onPagination(direction) {

            const targetUrl = direction === 'next' ? this.next_page : this.prev_page;

            if (targetUrl) {

                const url = new URL(targetUrl);

                const pageValue = url.searchParams.get("page");

                this.endPoint = this.updateQueryParam(this.endPoint, "page", pageValue);
            }
        },

        updateQueryParam(url, param, value) {

            url = url.replace(/([?&])page=\d+/, '');

            const separator = url.includes('?') ? '&' : '?';

            return `${url}${separator}${param}=${value}`;
        },

        onLimitChange() {

            this.endPoint = this.updateQueryParam(this.endPoint, "page", 1)
        }
    },

    components : {

        'simple-pagination': SimplePagination,

        'loader': PageLoader
    }
};

</script>

<style type="text/css">

.VueTables__row a {
    text-decoration: none !important;
}

table{
    border-collapse: collapse;
}
.datatable{
    padding-top:10px !important;
    padding-bottom: 45px !important;
}
.VueTables__search-field input, .globe-search{
    width : 300px !important;
}

.VueTables__search{
    float : right;
}

.datatable .VueTables__search{
    display: none;
}

.VueTables__limit{
    float : left !important;
    margin-left: -7px;
}
.VuePagination__pagination{
    margin-top: -5px !important;
    margin-right: -15px !important;
    float: right !important;
}
.VuePagination{
    margin-top: 10px !important;
}
.VuePagination__count {
    display: contents !important;
    margin-top: -10px !important;
}
.VuePagination .text-center{
    text-align: left !important;
    width: inherit;
}
/*.undefined{*/
/*	margin-left: 10px !important;*/
/*}*/
.VueTables__columns-dropdown button {
    background: none !important;
    border: 1px solid #d4d3d3 !important;
    margin-right: 5px !important;
}
.VueTables__columns-dropdown ul li a input{
    width: 13px; height: 13px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -3px;
    overflow: hidden;
}
.overlay-loader {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: white;
    opacity: 0.8;
    filter: blur(5px);
}

.clip-loader {
    position: absolute;
    left: 50%;
    right: 50%;
    bottom: 70%;
    top: 30%;
}
.faveo-datatable-loader {
    margin-top: 30px;
    margin-bottom: 30px;
}
.VueTables__table {
    font-size: 14px !important;
}

/*.VueTables__table th{
    font-weight: 500 !important;
}*/
.pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.VueTables__sort-icon {
    padding-left: 10px !important;
    cursor: pointer !important;
}

.VueTables__limit-field .form-control{
    cursor: pointer!important;
}

.VueTables__limit-field label{
    display: none !important;
}
</style>

<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Configure general software settings, enable and disable individual options.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="settings"/>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <dynamic-select :label="trans('smart_reports')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="smart_reports" :elements="smartReports" :value="smartReportsType" :onChange="onChange" >
                    </dynamic-select>

                    <dynamic-select :label="trans('smart_tables')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="smart_tables" :elements="smartTables" :value="smartTablesType" :onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('records_per_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="records_per_page" :elements="recordsPerPage" :value="recordsPerPageType" :onChange="onChange" >
                    </dynamic-select>

                    <dynamic-select :label="trans('records_on_index_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="records_on_index_page" :elements="recordIndexPage" :value="recordIndexPageType" :onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('search_result_limit')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="search_result_limit" :elements="searchLimit" :value="searchLimitType" :onChange="onChange" >
                    </dynamic-select>

                    <dynamic-select :label="trans('archive_older_records')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="archive_older_records" :elements="archiveOlderRecords" :value="archiveOlderRecordsType" :onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('timezone')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="license_storage_type" :elements="storageTypes" :value="selectedStorageType" :onChange="onChange" >
                    </dynamic-select>

                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-default" @click="onSubmit()"><i :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

import { successHandler, errorHandler } from 'helpers/responseHandler';

import  { getIdFromUrl } from 'helpers/extraLogics';

import moment from 'moment'

export default {

    name : 'General Settings',

    data() {

        return {

            title : 'general_settings',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment : moment,

            smartReports: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            smartReportsType: null,

            smartTables: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            smartTablesType: null,

            recordsPerPage: [
                {name: '10 Records', value: '1'},
                {name: '25 Records', value: '2'},
                {name: '50 Records', value: '3'},
                {name: '100 Records', value: '4'},
                {name: '200 Records', value: '5'},
                {name: '500 Records', value: '6'}
            ],
            recordsPerPageType: null,

            recordIndexPage: [
                {name: '1 Records', value: '1'},
                {name: '3 Records', value: '2'},
                {name: '5 Records', value: '3'},
                {name: '10 Records', value: '4'}
            ],
            recordIndexPageType: null,

            searchLimit: [
                {name: '10 Records', value: '1'},
                {name: '25 Records', value: '2'},
                {name: '50 Records', value: '3'},
                {name: '100 Records', value: '4'},
                {name: '200 Records', value: '5'},
                {name: '500 Records', value: '6'}
            ],
            searchLimitType: null,

            archiveOlderRecords: [
                {name: 'Disabled', value: 'disabled'},
                {name: '7 Days', value: '2'},
                {name: '14 Days', value: '3'},
                {name: '30 Days', value: '4'},
                {name: '60 Days', value: '5'},
                {name: '90 Days', value: '6'},
                {name: '180 Days', value: '2'},
                {name: '365 Days', value: '3'},
                {name: '730 Days', value: '4'}
            ],
            archiveOlderRecordsType: null,
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.loadData();
    },

    methods : {

        loadData() {

            this.loading = true;

            this.hasDataPopulated = false;

            Promise.all([this.getProducts(),this.getClients()]).then((values) => {

                [this.productOptions, this.clientOptions] = values;

                this.loading = false;

                this.hasDataPopulated = true;

            }).catch(function (error) {

                this.loading = false;

                this.hasDataPopulated = true;
            });
        },

        onChange(){

        },

        getProducts() {

        },

        getClients() {

        },

        onSubmit(){

        }
    },

    components : {

        "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
    }
}
</script>

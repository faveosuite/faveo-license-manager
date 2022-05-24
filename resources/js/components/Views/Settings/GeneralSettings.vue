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
                                    :required="false" name="smart_tables" :elements="smartTables" :value="smartTablesType" 
                                    :onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('records_per_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="records_per_page" :elements="recordsPerPage" :value="recordsPerPageType" 
                                    optionLabel="title"
                                    :onChange="onChange" >
                    </dynamic-select>

                    <dynamic-select :label="trans('records_on_index_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="records_on_index_page" :elements="recordIndexPage" :value="recordIndexPageType" 
                                    optionLabel="title":onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('search_result_limit')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="search_result_limit" :elements="searchLimit" :value="searchLimitType" 
                                    optionLabel="title":onChange="onChange" >
                    </dynamic-select>

                    <dynamic-select :label="trans('archive_older_records')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="archive_older_records" :elements="archiveOlderRecords" :value="archiveOlderRecordsType" 
                                    optionLabel="title":onChange="onChange" >
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('timezone')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="timezones" :elements="timezones" :value="selectedTimezone" :onChange="onChange" optionLabel="title" >
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

            settingId: 'new',

            timezones: [],

            selectedTimezone: null,

            smartReports: [
                {name: 'Enabled', value: 1},
                {name: 'Disabled', value: 0}
            ],

            smartReportsType: null,

            smartTables: [
                {name: 'Enabled', value: 1},
                {name: 'Disabled', value: 0}
            ],

            smartTablesType: null,

            recordsPerPage: [],

            recordsPerPageType: null,

            recordIndexPage: [],

            recordIndexPageType: null,

            searchLimit: [],

            searchLimitType: null,

            archiveOlderRecords: [ ],

            archiveOlderRecordsType: null,
        }
    },

    async beforeMount() {

        const path = window.location.pathname
        await this.getGeneralDropDownOptions()

        this.loadData();
    },

    methods : {

         async loadData() {

            this.loading = true;

            this.hasDataPopulated = false;

            await this.$store.dispatch('fetchSettings');

            this.setFormData()

            this.hasDataPopulated = true;


            this.loading = false;
           
        },
        async getGeneralDropDownOptions(){
            this.loading = true;
             return await axios.get("/api/admin/generalDropdown").then((res) => {
                 const options=res.data
                if(options.Timezon){
                    this.timezones= options.Timezon
                }
                if(options['records on admin page']){
                    this.recordsPerPage= options['records on admin page']
                }
                if(options['records on index page']){
                    this.recordIndexPage= options['records on index page']
                }
                if(options['records on search page']){
                    this.searchLimit= options['records on search page']
                }
                if(options['records on archieve days']){
                    this.archiveOlderRecords= options['records on archieve days']
                }

               
                this.loading = false;
                

            }).catch((err) => {

                this.loading = false;

            });
        },

        setFormData(){
            const generalSetting = this.$store.getters['getGeneralSettings']

            if(generalSetting){

                this.settingId = generalSetting.SETTING_ID ?? 'new'

                this.smartReportsType = this.smartReports.find((opt)=>{
                    return opt.value === generalSetting.SMART_REPORTS
                })

                this.smartTablesType = this.smartTables.find((opt)=>{
                    return opt.value === generalSetting.SMART_TABLES
                })

                this.selectedTimezone = this.timezones.find((opt)=>{
                    return opt.value === generalSetting.TIMEZONE
                })

                this.archiveOlderRecordsType = this.archiveOlderRecords.find((opt)=>{
                    return opt.value === generalSetting.RECORDS_ARCHIVE_DAYS
                })

                this.recordsPerPageType = this.recordsPerPage.find((opt)=>{
                    return opt.value === generalSetting.RECORDS_ON_ADMIN_PAGE
                })

                this.recordIndexPageType = this.recordIndexPage.find((opt)=>{
                    return opt.value === generalSetting.RECORDS_ON_INDEX_PAGE
                })

                this.searchLimitType = this.searchLimit.find((opt)=>{
                    return opt.value === generalSetting.RECORDS_ON_SEARCH_PAGE
                })
            }
        },
       
        onChange(value, name){

           if(name === 'smart_reports'){
                this.smartReportsType = value
            } else if(name === 'smart_tables'){
                this.smartTablesType = value
            } else if(name === 'records_per_page'){
                this.recordsPerPageType = value
            } else if(name === 'records_on_index_page'){
                this.recordIndexPageType = value
            } else if(name === 'search_result_limit'){
                this.searchLimitType = value
            } else if(name === 'archive_older_records'){
                this.archiveOlderRecordsType = value
            } else if(name === 'timezones'){
                this.selectedTimezone = value
            }  
        },

        getProducts() {

        },

        getClients() {

        },

        async onSubmit(){

            const formData = {

                SMART_REPORTS:  this.smartReportsType ? this.smartReportsType.value : null,

                SMART_TABLES: this.smartTablesType? this.smartTablesType.value : null,

                TIMEZONE: this.selectedTimezone? this.selectedTimezone.value : null,

                RECORDE_ARCHIVE_DAYS: this.archiveOlderRecordsType ? this.archiveOlderRecordsType.value : null,

                RECORDE_ON_ADMIN_PAGE: this.recordsPerPageType ? this.recordsPerPageType.value : null,

                RECORDE_ON_INDEX_PAGE:  this.recordIndexPageType ? this.recordIndexPageType.value : null,

                RECORDE_ON_SEARCH_PAGE: this.searchLimitType ? this.searchLimitType.value : null,
            }

            await axios.post(`/api/admin/generalsettings/${this.settingId}`,formData).then(async (res) => {


                successHandler(res,'settings');

                await this.$store.dispatch('fetchSettings');
                
                this.loading = false;
               


            }).catch((err) => {

                this.loading = false;

                errorHandler(err,'settings');
            });
        }
    },

    components : {

        "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
    }
}
</script>

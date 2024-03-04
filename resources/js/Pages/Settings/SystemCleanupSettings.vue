<template>

    <div class="col-sm-12">

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="settings" />

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{lang('system_cleanup_crons')}}</h3>
            </div>

            <div class="card-body">

                <p>{{lang('copy-cron-command-description')}}</p>

                <div class="card p-4 bg-light">

                    <div class="row">

                        <div class="col-sm-2">

                            <span class="text-xl">*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*&nbsp;&nbsp;*</span>
                        </div>

                        <div class="col-sm-4">

                            <select v-if="php_path != 'other'" class="form-control" v-model="php_path">

                                <option disabled value="">{{lang('specify-php-executable')}}</option>

                                <template v-for="(path, index) in phpPaths">

                                    <option :value="path">{{path}}</option>
                                </template>

                                <option value="other">{{lang('other')}}</option>
                            </select>

                            <div v-if="php_path == 'other'" class="input-group">

                                <input type="text" class="form-control" v-model="custom_php_path" :placeholder="lang('specify-php-executable')">

                                <div class="input-group-append">

                                    <a href="javascript:;" class="input-group-text" @click="clearPhpPath"><i class="fas fa-times"></i></a>

                                </div>
                            </div>
                        </div>

                        <div class="col-sm-5"><span class="text-md">{{cron_path}}</span></div>

                        <div class="col-sm-1">

                            <span v-if="!copying" v-tooltip="lang('verify-and-copy-command')" @click="copyCommand()" class="pointer">

                               <i class="far fa-clipboard fa-2x"></i>
                            </span>

                            <span v-if="copying" class="pointer"><i class="fas fa-circle-notch fa-spin fa-2x"></i></span>

                        </div>
                    </div>
                </div>

                <div class="row">

                    <template v-for="job in conditions">

                        <div class="col-md-6">

                            <div class="info-box">

                                <span class="info-box-icon bg-info"><i :class="job.icon"></i></span>

                                <div class="info-box-content">

                                    <div class="row">

                                        <div class="col-md-7">
                                            <div class="form-group mt-4">

                                                <label class="new_label" name="share">

                                                    <input class="checkbox_align" type="checkbox" name="status" id="status_job" v-model="job.status">
                                                    <span class="scenario-text">&nbsp;{{lang(job.scenario)}}</span>
                                                </label>

                                                <tool-tip :message="lang(job.job_info)" size="small"></tool-tip>
                                            </div>
                                        </div>
                                        <div v-if="job.status" class="col-md-5 mt-13" id="fetching-command-block">

                                            <div class="row">
                                                <select class="form-control col-sm-12 mt-1" v-model="job.value">

                                                    <option value="">{{lang('Select')}}</option>

                                                    <template v-for="time in timeOptions">

                                                        <option :value="time.id">{{time.name}}</option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </template>
                </div>
            </div>
            <div class="card-footer" >

                <button type="button" class="btn btn-primary" @click="onClick" :disabled="pageLoad">

                    <i class="fas fa-save"></i> {{lang('save')}}
                </button>
            </div>
        </div>
        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                   <dynamic-select :label="lang('remove_callbacks_older_than')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" name="DATABASE_CLEANUP_CALLBACKS"
                                    :elements="removeOlderCallbacks" :value="removeOlderCallbacksOptions" optionLabel="title"
                                    :onChange="onChange">

                    </dynamic-select>
                    <dynamic-select :label="lang('remove_crack_reports')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" name="DATABASE_CLEANUP_REPORTS_MAIN" :elements="removeLicenseReports"
                                    :value="removeLicenseReportsOptions" optionLabel="title" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('remove_system_reports')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" name="DATABASE_CLEANUP_REPORTS_SYSTEM" :elements="removeSystemReports"
                                    :value="removeSystemReportsOptions" optionLabel="title" :onChange="onChange">

                    </dynamic-select>

                    <dynamic-select :label="lang('license_reports')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" name="DATABASE_CLEANUP_REPORTS_LICENSES" :elements="removeSystems"
                                    :value="removeSystem" optionLabel="title" :onChange="onChange">

                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('remove_versions')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" name="DATABASE_CLEANUP_VERSIONS"
                                    :elements="removeVersions" optionLabel="title" :value="removeVersionsType"
                                    :onChange="onChange">
                    </dynamic-select>
                </div>

            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit"><i
                    :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
 </template>

 <script>

 import axios from 'axios'

 import { successHandler, errorHandler } from '../../helpers/responseHandler';

 import {getIdFromUrl, lang} from '../../helpers/extraLogics';

 import moment from 'moment'

 import TextField from "../../components/Reusable/FormField/TextField.vue";

 import NumberField from "../../components/Reusable/FormField/NumberField.vue";

 import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

 import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

 import copy from 'clipboard-copy';

 import {systemCleanupSettings} from "../../helpers/validator/SystemCleanupSettings";


 export default {


    name: 'System-Cleanup-Settings',


    data() {


        return {

            title: 'system_cleanup_settings',

            iconClass: 'fas fa-save',

            btnName: 'save',

            hasDataPopulated: false,

            loading: false,

            apiEndpoint: '',

            moment: moment,

            autoSystemCleanupType: null,

            removeOlderCallbacks: [],

            removeOlderCallbacksOptions: null,

            removeLicenseReports: [],

            removeLicenseReportsOptions: null,

            removeSystemReports: [],

            removeSystemReportsOptions: null,

            removeVersionsType: null,

            removeVersions: [],

            removeSystems: [],

            removeSystem: null,

            removeLicenseReportsType: null,

            pageLoad : false,

            conditions : '',

            exec_enabled : false,

            cron_path : '',

            phpPaths : '',

            php_path : '',

            labelStyle : { display : 'none' },

            custom_php_path : '',

            copying : false,

            timeOptions : [],

            intervalTimeData: [],

        }
    },


    async beforeMount() {


        const path = window.location.pathname


        await this.getCleanUpDropdownOptions()


        this.getCronData();


        this.getTimeOptions();


        this.loadData();
    },


    methods: {

        isValid() {

            const { errors, isValid } = systemCleanupSettings(this.$data);

            return isValid;
        },

        findOptionByValue(options, value) {
            if (options) {
                return options.find(option => option.value === value);
            } else {
                return null;
            }
        },

        lang: lang,

        async loadData() {

            this.loading = true;

            this.hasDataPopulated = false;

            await this.$store.dispatch('fetchSettings');

            this.setFormData()

            this.hasDataPopulated = true;

            this.loading = false;
        },


        async getCleanUpDropdownOptions() {
            try {
                this.loading = true;
                const res = await axios.get("/api/admin/cleanSettings");
                const options = res.data;

                this.removeOlderCallbacks = options['database cleanup callbacks'] || [];
                this.removeLicenseReports = options['database cleanup reports main'] || [];
                this.removeSystemReports = options['database cleanup reports system'] || [];
                this.removeSystems = options['database cleanup reports license'] || [];
                this.removeVersions = options['database cleanup versions'] || [];

                this.loading = false;

                successHandler(res, 'settings')
            } catch (err) {
                this.loading = false;
                errorHandler(err, 'settings');
            }
        },


        setFormData() {
            const cleanUpSettings = this.$store.getters['getCleanUpSettings'];

            if (cleanUpSettings) {
                this.settingId = cleanUpSettings.SETTING_ID ?? 'new';
                this.removeOlderCallbacksOptions = this.findOptionByValue(this.removeOlderCallbacks, cleanUpSettings.DATABASE_CLEANUP_CALLBACKS);
                this.removeLicenseReportsOptions = this.findOptionByValue(this.removeLicenseReports, cleanUpSettings.DATABASE_CLEANUP_REPORTS_MAIN);
                this.removeSystemReportsOptions = this.findOptionByValue(this.removeSystemReports, cleanUpSettings.DATABASE_CLEANUP_REPORTS_SYSTEM);
                this.removeVersionsType = this.findOptionByValue(this.removeVersions, cleanUpSettings.DATABASE_CLEANUP_VERSIONS);
                this.removeSystem = this.findOptionByValue(this.removeSystems, cleanUpSettings.DATABASE_CLEANUP_REPORTS_LICENSES);
            }
        },



        onChange(value, name) {
            const propertyMap = {
                'DATABASE_CLEANUP_CALLBACKS': 'removeOlderCallbacksOptions',
                'DATABASE_CLEANUP_REPORTS_MAIN': 'removeLicenseReportsOptions',
                'DATABASE_CLEANUP_REPORTS_SYSTEM': 'removeSystemReportsOptions',
                'DATABASE_CLEANUP_VERSIONS': 'removeVersionsType',
                'DATABASE_CLEANUP_REPORTS_LICENSES': 'removeSystem'
            };

            if (propertyMap.hasOwnProperty(name)) {
                this[propertyMap[name]] = value;
            }
        },

        async onSubmit() {
            if (this.isValid()) {

                this.loading = true

                const formData = {

                    DATABASE_CLEANUP_CALLBACKS: this.removeOlderCallbacksOptions ? this.removeOlderCallbacksOptions.value : null,

                    DATABASE_CLEANUP_REPORTS_LICENSES: this.removeSystem ? this.removeSystem.value : null,

                    DATABASE_CLEANUP_REPORTS_MAIN: this.removeLicenseReportsOptions ? this.removeLicenseReportsOptions.value : null,

                    DATABASE_CLEANUP_REPORTS_SYSTEM: this.removeSystemReportsOptions ? this.removeSystemReportsOptions.value : null,

                    DATABASE_CLEANUP_VERSIONS: this.removeVersionsType ? this.removeVersionsType.value : null,


                }


                await axios.post(`api/admin/saveintervalSettings`, formData).then(async (res) => {

                    successHandler(res, 'settings');

                    await this.$store.dispatch('fetchSettings');

                    this.loading = false;


                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'settings');
                });
            }
        },

        async getTimeOptions() {
            this.timeOptions = (await axios.get('/api/admin/cronTimeCommands')).data.data.cron_time_commands;
        },

        getCronData() {

            this.loading = true;

            axios.get('/api/admin/cleanupSettings').then(res=>{

                this.cron_path = res.data.data.cron_path;

                this.conditions = res.data.data.conditions;

                this.conditions.map(obj=>{

                    obj['status'] = obj.status === 1 ? true : false;

                    if(obj.value.includes('dailyAt')) {

                        obj['daily_time'] = obj.value.split(',')[obj.value.split(',').length - 1 ];

                        obj['value'] = 'dailyAt';
                    }
                });

                this.phpPaths = res.data.data.php_bin_paths;

                this.hasDataPopulated = true;

                this.loading = false;

                successHandler(res,'cron-settings');
            }).catch(err=>{

                this.hasDataPopulated = true;

                this.loading = false;
            })
        },

        clearPhpPath() {

            this.php_path = '';

            this.custom_php_path = '';
        },


        copyCommand() {

            this.copying = true;

            let data = {};

            data['path'] = this.custom_php_path ? this.custom_php_path : this.php_path;

            axios.post('/api/admin/verify-php-path', data).then(res=>{


                copy('* * * * * ' + (this.custom_php_path ? this.custom_php_path : this.php_path) +' '+this.cron_path);

                this.copying = false;

                successHandler(res,'settings');

            }).catch(err=>{

                this.copying = false;

                errorHandler(err,'settings');
            })
        },


        onClick() {

            const data = {};

            let jobObj = {};

            this.conditions.forEach((obj)=>{

                jobObj[obj.scenario] = { status : obj.status ? 1 : 0, value : obj.status ? obj.value : ''}

                if(obj.value.includes('dailyAt') && obj.daily_time && obj.status) {

                    jobObj[obj.scenario]['time'] = obj.daily_time;

                }
            });

            data['conditions'] = jobObj;

            this.pageLoad = true;

            axios.post('/api/admin/cleanupsettings', data).then(res => {

                this.pageLoad = false;

                successHandler(res,'settings');

                this.getCronData();

            }).catch(err => {

                successHandler(err,'settings');

                this.pageLoad = false;

            });
        },
    },

    components: {

        "text-field": TextField,

        "number-field": NumberField,

        "static-select": StaticSelect,

        "dynamic-select": DynamicSelect,
    }
 }
 </script>

<style scoped>
.label_align1 {
    display: block; padding-left: 15px; text-indent: -15px; padding-top: 6px;
}
.checkbox_align {
    width: 13px; height: 16px; padding: 0; margin:0; vertical-align: bottom; position: relative; top: -0.3rem; overflow: hidden;
}
.mt-13 { margin-top: 13px;}
.scenario-text{ font-size: 12px; font-weight: bold; margin-left: 2px}
.pointer{cursor: pointer!important;}
</style>


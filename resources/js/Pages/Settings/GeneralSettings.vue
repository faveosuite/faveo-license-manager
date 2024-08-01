<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="google-recaptcha" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang('google_recaptcha_settings')}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('google_site_key')" v-tooltip="lang('recaptcha')" :value="google_site_key"
                                :onChange="onChange" name="google_site_key" type="text" :required="true" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('google_secret_key')" v-tooltip="lang('recaptcha')" :value="google_secret_key"
                                :onChange="onChange" :required="true" name="google_secret_key" type="password" classname="col-sm-6">

                    </text-field>

                    <radio-option :label="lang('status')" :value="recaptcha_status" :onChange="onChange"
                                  :options="[{name:'active', value:1}, {name:'inactive', value:0}]" >

                    </radio-option>

                </div>

                <div>
                    <br>
                    <div v-if="!verified" class="row">

                        <div class="col-sm-6">

                            <custom-loader :animation-duration="4000" :size="30"/>
                        </div>
                    </div>

                    <recaptcha-field v-if="verified && google_site_key" :node="{}"
                                     name="recaptcha"
                                     :siteKeyValue="google_site_key"
                                     captchaVersion="v3"
                                     :verifyCaptcha="verifyCaptcha">

                    </recaptcha-field>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary mr-2" :disabled="!recaptchaVerified" @click="onSubmit" > <i :class="iconClass"></i> {{ trans(btnName) }}</button>
            </div>

        </div>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang('general_settings')}}</h3>

                <tool-tip :message="lang('general_settings_configuration')" size="medium"></tool-tip>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('agora_invoicing_url')" :value="agora_invoicing_url" :onChange="onChange"
                                name="agora_invoicing_url" :required="true" type="text" classname="col-sm-6">

                    </text-field>

                    <dynamic-select name="timezone" apiEndpoint="/api/admin/timezones" :multiple="false" label="Timezone Settings" :onChange="onChange"
                                    classname="col-sm-6" :value="timezone" optionLabel="location" :required="true">

                    </dynamic-select>
                </div>

                <hr>

                <div class="row">

                    <dynamic-select name="date_format" apiEndpoint="api/admin/date-formats" :multiple="false" label="Date Format Settings" :onChange="onChange"
                                    classname="col-sm-6" :value="date_format" optionLabel="format" :required="true" :showPreview="previewMethod(date_format)">

                    </dynamic-select>

                    <dynamic-select name="time_format" apiEndpoint="api/admin/time-formats" :multiple="false" label="Time Format Settings" :onChange="onChange"
                                    classname="col-sm-6" :showPreview="timeFormat(time_format)" :value="time_format" optionLabel="hours" :required="true">

                    </dynamic-select>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary mr-2" @click="onSubmit" > <i :class="iconClass"></i> {{ trans(btnName) }}</button>

                <button class="btn btn-danger" @click="onReset"> <i :class="iconUndo"></i> {{ trans('Reset') }}</button>
            </div>

        </div>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang('logo_and_favicon')}}</h3>

                <tool-tip :message="lang('logo_icon_config')" size="medium"></tool-tip>
            </div>

            <div class="card-body">

                <div class="row">

                    <label class="label_align col-md-4 text-center">
                        <input class="checkbox_align" type="checkbox" name="defaulticon" v-model="defaulticon">&nbsp;{{lang('use_default')}}
                    </label>

                    <label class="label_align col-md-4 text-center">
                        <input class="checkbox_align" type="checkbox" name="defaultlogo" v-model="defaultlogo">&nbsp;{{lang('use_default')}}
                    </label>

                    <label class="label_align col-md-4 text-center">
                        <input class="checkbox_align" type="checkbox" name="uselogo" v-model="uselogo">&nbsp;{{lang('use_logo')}}
                    </label>
                </div>

                <div class="row">

                    <image-upload :label="lang('favicon')" :labelStyle="logoStyle" :value="icon"
                                  name="icon" :onChange="onChange" btnName="change_icon" componentName="google-recaptcha"
                                  classname="col-sm-4 text-center" :is_default="defaulticon">
                    </image-upload>

                    <image-upload :label="lang('admin_logo')" :labelStyle="logoStyle"
                                  :value="logo_admin_agent" componentName="google-recaptcha"
                                  name="logo_admin_agent" :onChange="onChange"
                                  classname="col-sm-4 text-center" :is_default="defaultlogo">
                    </image-upload>

                    <image-upload :label="lang('client_logo')" :labelStyle="logoStyle" :value="logo"
                                  name="logo" :onChange="onChange" componentName="google-recaptcha"
                                  classname="col-sm-4 text-center">
                    </image-upload>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary mr-2" @click="onSubmit" > <i :class="iconClass"></i> {{ trans(btnName) }}</button>
            </div>

        </div>
    </div>
</template>

<script>

import axios from 'axios'

import DatatableDynamicSelect from "../../components/Reusable/FormField/DatatableDynamicSelect.vue";

import { successHandler, errorHandler } from '../../helpers/responseHandler';

import moment from 'moment'

import {lang} from "../../helpers/extraLogics";

import TextField from "../../components/Reusable/FormField/TextField.vue";

import RecaptchaField from "../../components/Reusable/FormField/RecaptchaField.vue";

import ImageUpload from "../../components/Reusable/FormField/ImageUpload.vue";

import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";

import {validateGeneralSettings} from "../../helpers/validator/validateGeneralSettings";

export default {

    name: 'google-recaptcha',

    data() {

        return {

            iconClass: 'fas fa-save',

            iconUndo : 'fas fa-undo',

            btnName: 'save',

            logoStyle : { visibility : 'hidden' },

            verified: true,

            recaptchaVerified: '',

            hasDataPopulated: false,

            recaptcha_status: 0,

            loading: true,

            apiEndpoint: '',

            moment: moment,

            responseData: '',

            google_site_key: '',

            google_secret_key: '',

            agora_invoicing_url: '',

            time_format : '',

            timezone : '',

            date_format : '',

            icon: '',

            logo_admin_agent: '',

            logo: '',

            defaulticon: 0,

            defaultlogo: 0,

            uselogo: 0

        }
    },

    beforeMount() {

        this.getProducts();
    },

    methods: {
        lang,

        verifyCaptcha(value) {

            this.recaptchaVerified = value;
        },

        previewMethod(value) {

            return value ? moment(new Date()).format(value.js_format) : ''
        },

        timeFormat(value) {

            return value ? moment(new Date()).format(value.js_format) : ''
        },

        getProducts() {

            this.loading = true;

            this.hasDataPopulated = false;

            axios.get('/api/admin/common-setting/get').then(res => {

                this.updateStatesWithData(res.data.data);

                this.loading = false;

                this.hasDataPopulated = true;

            }).catch(err=>{

                this.loading = false;
            })
        },

        onChange(value, name) {

            if(name === 'google_site_key' || name === 'google_secret_key') {

                this.recaptchaVerified = '';

                this.verified = false;

                this[name] = value;

                setTimeout(()=>{

                    this.verified = true;
                },2000)
            } else {

                switch (name) {
                    case 'icon':
                        this.icon = value.image;
                        let icon = value;
                        this.selectedIcon = icon.file, icon.name;
                        break;
                    case 'logo_admin_agent':
                        this.logo_admin_agent = value.image;
                        let logo_admin_agent = value;
                        this.selectedLogoAdminAgent = logo_admin_agent.file, logo_admin_agent.name;
                        break;
                    case 'logo':
                        this.logo = value.image;
                        let logo = value;
                        this.selectedLogo = logo.file, logo.name;
                        break;
                    default:
                        this[name] = value;
                }
            }
        },

         onSubmit() {

            if(this.isValid()) {

                this.loading = true;

                const formData = {};

                formData['google_site_key'] = this.google_site_key;

                formData['google_secret_key'] = this.google_secret_key;

                formData['g-recaptcha-response'] = this.recaptchaVerified;

                formData['agora_invoicing_url'] = this.agora_invoicing_url;

                formData['date_format'] = this.date_format.id;

                formData['time_format'] = this.time_format.id;

                formData['timezone'] = this.timezone.id

                axios.post('/api/admin/common-setting', formData).then(res => {

                    this.loading = false;

                    successHandler(res,'google-recaptcha');

                    this.getProducts()

                }).catch(err => {

                    this.loading = false;

                    errorHandler(err,'google-recaptcha');
                });

                this.loading = false;

            }
        },

        isValid() {

            const {errors, isValid} = validateGeneralSettings(this.$data);

            return isValid;
        },

         onReset() {

            this.loading = true;

            axios.post('/api/admin/common-setting/reset').then(res => {

                this.loading = false;

                successHandler(res,'google-recaptcha');

                this.getProducts()

            }).catch(err => {

                this.loading = false;

                errorHandler(err,'google-recaptcha');
            });

            this.loading = false;
        },

        updateStatesWithData(data) {

            const self = this;

            const stateData = this.$data;

            Object.keys(data).map(key => {

                if (stateData.hasOwnProperty(key)) {

                    self[key] = data[key];
                }
            });
        },
    },

    components: {

        "text-field": TextField,

        "dynamic-select" : DatatableDynamicSelect,

        "recaptcha-field": RecaptchaField,

        "image-upload": ImageUpload,

        "radio-option": RadioButton

    }
}
</script>

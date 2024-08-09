<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="google-recaptcha" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang('general_settings')}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('google_site_key')" :disabled="!recaptcha_status" :hint="lang('recaptcha')" :value="google_site_key"
                                :onChange="onChange" name="google_site_key" type="text" :required="true" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('google_secret_key')" :disabled="!recaptcha_status" :hint="lang('recaptcha')" :value="google_secret_key"
                                :onChange="onChange" :required="true" name="google_secret_key" type="password" classname="col-sm-6">

                    </text-field>

                    <radio-option :label="lang('status')" :hint="lang('recaptcha_status')" name="recaptcha_status" :value="recaptcha_status" :onChange="onChange"
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

                <div class="card card-light" v-if="hasDataPopulated">

                    <div class="card-header">

                        <h3 class="card-title">{{lang('logo_and_favicon')}}</h3>

                        <tool-tip :message="lang('logo_icon_config')" size="medium"></tool-tip>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <label class="label_align col-md-4 text-center">
                                <input class="checkbox_align" type="checkbox" name="defaultIcon" v-model="defaultIcon">&nbsp;{{lang('use_default')}}
                            </label>

                            <label class="label_align col-md-4 text-center">
                                <input class="checkbox_align" type="checkbox" name="defaultLogo" v-model="defaultLogo">&nbsp;{{lang('use_default')}}
                            </label>

                            <label class="label_align col-md-4 text-center">
                                <input class="checkbox_align" type="checkbox" name="useLogo" v-model="useLogo">&nbsp;{{lang('use_default')}}
                            </label>
                        </div>

                        <div class="row">

                            <image-upload :label="lang('favicon')" :labelStyle="logoStyle" :value="icon"
                                          name="icon" :onChange="onChange" btnName="change_icon" componentName="google-recaptcha"
                                          classname="col-sm-4 text-center" :is_default="defaultIcon">
                            </image-upload>

                            <image-upload :label="lang('admin_logo')" :labelStyle="logoStyle"
                                          :value="admin_logo" componentName="google-recaptcha"
                                          name="admin_logo" :onChange="onChange"
                                          classname="col-sm-4 text-center" :is_default="defaultLogo">
                            </image-upload>

                            <image-upload :label="lang('client_logo')" :labelStyle="logoStyle" :value="client_logo"
                                          name="client_logo" :onChange="onChange" componentName="google-recaptcha"
                                          classname="col-sm-4 text-center" :is_default="useLogo">
                            </image-upload>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary mr-2" :disabled="!recaptchaVerified && recaptcha_status" @click="onSubmit" > <i :class="iconClass"></i> {{ trans(btnName) }}</button>
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

            admin_logo: '',

            client_logo: '',

            defaultIcon: 0,

            defaultLogo: 0,

            useLogo: 0,

            selectedIcon: '',

            selectedAdminLogo: '',

            selectedClientLogo: ''

        }
    },

    beforeMount() {

        this.getProducts();
    },

    methods: {
        lang,

        verifyCaptcha(value) {

            let element = document.getElementsByClassName('grecaptcha-badge');
            element[0].setAttribute('id', 'grecaptcha_badge');
            document.getElementById('grecaptcha_badge').style.visibility = 'visible';
            document.getElementById('grecaptcha_badge').style.display = 'inline';

            this.recaptchaVerified = value;
        },

        previewMethod(value) {

            return value ? moment(new Date()).format(value.js_format) : ''
        },

        timeFormat(value) {

            return value ? moment(new Date()).format(value.js_format) : ''
        },

        getProducts(from) {

            this.loading = true;

            this.hasDataPopulated = false;

            axios.get('/api/admin/common-setting/get').then(res => {

                this.updateStatesWithData(res.data.data);

                if(from === 'update') {

                    this.$store.dispatch('setAdminData', res.data.data.admin_logo)
                }

                this.loading = false;

                this.hasDataPopulated = true;

            }).catch(err=>{

                this.loading = false;
            })
        },

        onChange(value, name) {

                if(this.recaptcha_status) {

                    this.recaptchaVerified = '';

                    this.verified = false;

                    setTimeout(()=>{

                        this.verified = true;
                    },2000)
                }

                switch (name) {
                    case 'icon':
                        this.icon = value.image;
                        this.selectedIcon = value;
                        break;
                    case 'admin_logo':
                        this.admin_logo = value.image;
                        this.selectedAdminLogo = value;
                        break;
                    case 'client_logo':
                        this.client_logo = value.image;
                        this.selectedClientLogo = value;
                        break;
                    default:
                        this[name] = value;
                }
        },

        onSubmit() {

            if(this.isValid()) {

                this.loading = true;

                let fd = new FormData();

                if(this.recaptcha_status) {

                    fd.append('google_site_key', this.google_site_key);

                    fd.append('google_secret_key', this.google_secret_key);

                    fd.append('g-recaptcha-response', this.recaptchaVerified);
                }

                fd.append('recaptcha_status', this.recaptcha_status);

                fd.append('agora_invoicing_url', this.agora_invoicing_url);

                fd.append('date_format', this.date_format.id);

                fd.append('time_format', this.time_format.id);

                fd.append('timezone', this.timezone.id);

                fd.append('icon_default', this.defaultIcon);

                fd.append('admin_logo_default', this.defaultLogo);

                fd.append('client_logo_default', this.useLogo);

                if(this.selectedIcon){
                    fd.append('icon', this.selectedIcon.file,this.selectedIcon.name);
                }

                if(this.selectedAdminLogo){
                    fd.append('admin_logo', this.selectedAdminLogo.file,this.selectedAdminLogo.name);
                }

                if(this.selectedClientLogo){
                    fd.append('client_logo', this.selectedClientLogo.file,this.selectedClientLogo.name);
                }

                axios.post('/api/admin/common-setting', fd).then(res => {

                    this.loading = false;

                    successHandler(res,'google-recaptcha');

                    this.getProducts('update');

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

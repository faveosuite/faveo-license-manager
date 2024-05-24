<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="google-recaptcha" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('google_site_key')" :value="google_site_key" :onChange="onChange"
                                name="google_site_key" type="text" :required="true" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('google_secret_key')" :value="google_secret_key"
                                :onChange="onChange" :required="true" name="google_secret_key" type="password" classname="col-sm-6">

                    </text-field>
                </div>

                <hr>

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

<!--            <div class="card-footer">-->

<!--                <button class="btn btn-primary" @click="onSubmit(true,-->
<!--                '/api/admin/common-setting',-->
<!--                {-->
<!--                    google_site_key : this.google_site_key,-->
<!--                    google_secret_key: this.google_secret_key,-->
<!--                    agora_invoicing_url: this.agora_invoicing_url-->
<!--                }-->
<!--                )-->
<!--            "><i-->
<!--                    :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>-->
<!--                &nbsp;-->
<!--                <button class="btn btn-danger" @click="onSubmit(false, '/api/admin/common-setting/reset',{clear:1})"><i-->
<!--                    :class="iconUndo"></i>&nbsp;&nbsp;{{trans('Reset')}}</button>-->
<!--            </div>-->
            <div class="card-footer">

                <button class="btn btn-primary mr-2" @click="onSubmit" > <i :class="iconClass"></i> {{ trans(btnName) }}</button>

                <button class="btn btn-danger" @click="onReset"> <i :class="iconUndo"></i> {{ trans('Reset') }}</button>
            </div>

        </div>
    </div>
</template>

<script>

import axios from 'axios'

import DatatableDynamicSelect from "../../components/Reusable/FormField/DatatableDynamicSelect.vue";

import { successHandler, errorHandler } from '../../helpers/responseHandler';

import moment from 'moment'

import TextField from "../../components/Reusable/FormField/TextField.vue";

export default {

    name: 'google-recaptcha',

    data() {

        return {

            title: 'general-settings',

            iconClass: 'fas fa-save',

            iconUndo : 'fas fa-undo',

            btnName: 'save',

            hasDataPopulated: false,

            loading: true,

            apiEndpoint: '',

            moment: moment,

            responseData: '',

            google_site_key: '',

            google_secret_key: '',

            agora_invoicing_url: '',

            time_format : '',

            timezone : '',

            date_format : ''

        }
    },

    beforeMount() {
        this.getProducts();
    },

    methods: {

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

                this.updateStatesWithData(res.data.data ?? '');

                this.loading = false;

                this.hasDataPopulated = true;

            }).catch(err=>{

                this.loading = false;
            })
        },

        onChange(option, name) {

            this[name] = option ? option : '';
        },

        async onSubmit() {

            // this.loading = true
            //
            // await axios.post(url, formData).then((res) => {
            //
            //     successHandler(res, 'google-recaptcha');
            //
            //     this.loading = false;
            //
            // }).catch((err) => {
            //
            //     this.loading = false;
            //
            //     errorHandler(err, 'google-recaptcha');
            // });

                this.loading = true;

                const data = {};

                data['google_site_key'] = this.google_site_key;

                data['google_secret_key'] = this.google_secret_key;

                data['agora_invoicing_url'] = this.agora_invoicing_url;

                data['date_format'] = this.date_format.id;

                data['time_format'] = this.time_format.id;

                data['timezone'] = this.timezone.id

                axios.post('/api/admin/common-setting', data).then(res => {

                    this.loading = false;

                    successHandler(res,'google-recaptcha');

                    this.getProducts()

                }).catch(err => {

                    this.loading = false;

                    errorHandler(err,'google-recaptcha');
                });

                this.loading = false;
        },

        async onReset() {

            this.loading = true;

            const data = {};

            data['google_site_key'] = "";

            data['google_secret_key'] = "";

            data['agora_invoicing_url'] = "";

            data['date_format'] = "";

            data['time_format'] = "";

            data['timezone'] = ""

            axios.post('/api/admin/common-setting', data).then(res => {

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

        "dynamic-select" : DatatableDynamicSelect

    }
}
</script>

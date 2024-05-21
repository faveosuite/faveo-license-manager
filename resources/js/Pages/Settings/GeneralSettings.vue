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
                                name="google_site_key" type="text" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('google_secret_key')" :value="google_secret_key"
                                :onChange="onChange" name="google_secret_key" type="password" classname="col-sm-6"
                    >
                    </text-field>
                </div>

                <hr>

                <div class="row">
                    <text-field :label="trans('agora_invoicing_url')" :value="agora_invoicing_url" :onChange="onChange"
                                name="agora_invoicing_url" type="text" classname="col-sm-6">
                    </text-field>

<!--                    <dynamic-select :label="trans('timeformat')" :multiple="false" name="time_format" classname="col-sm-4"-->
<!--                                    apiEndpoint="/api/admin/timezones" :value="time_format" :onChange="onChange" :strlength="25"-->
<!--                                    :required="true">-->

<!--                    </dynamic-select>-->

                    <example-select name="name" apiEndpoint="/api/admin/timezones"
                                    label="Timezone Settings" :onChange="onChange"
                                    classname="col-sm-6" optionLabel="location" :required="true">

                    </example-select>
                </div>

                <hr>

                <div class="row">
                    <example-select name="name" apiEndpoint="api/admin/date-formats"
                                    label="Date Format Settings" :onChange="onChange"
                                    classname="col-sm-6" optionLabel="format" :required="true">

                    </example-select>

                    <example-select name="name" apiEndpoint="api/admin/time-formats"
                                    label="Time Format Settings" :onChange="onChange"
                                    classname="col-sm-6" optionLabel="hours" :required="true">

                    </example-select>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit(true,
                '/api/admin/common-setting',
                {
                    google_site_key : this.google_site_key,
                    google_secret_key: this.google_secret_key,
                    agora_invoicing_url: this.agora_invoicing_url
                }
                )
            "><i
                    :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
                &nbsp;
                <button class="btn btn-danger" @click="onSubmit(false, '/api/admin/common-setting/reset',{clear:1})"><i
                    :class="iconUndo"></i>&nbsp;&nbsp;{{trans('Reset')}}</button>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

import ExampleSelect from "../../components/Reusable/FormField/ExampleSelect.vue";

import DynamicSelect1 from "../../components/Reusable/FormField/DynamicSelect1.vue";

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

            loading: false,

            apiEndpoint: '',

            moment: moment,

            responseData: '',

            google_site_key: '',

            google_secret_key: '',

            agora_invoicing_url: ''

        }
    },

    beforeMount() {
        this.getProducts();
    },

    methods: {

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

        async onSubmit(clear=false,url,formData) {

            this.loading = true

            await axios.post(url, formData).then((res) => {

                successHandler(res, 'google-recaptcha');

                this.loading = false;

            }).catch((err) => {

                this.loading = false;

                errorHandler(err, 'google-recaptcha');
            });
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

        "example-select" : ExampleSelect,

        "dynamic-select" : DynamicSelect1

    }
}
</script>

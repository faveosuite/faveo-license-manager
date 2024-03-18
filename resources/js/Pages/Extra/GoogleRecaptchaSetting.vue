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
                                name="google_site_key" type="text" classname="col-sm-6" :required="true">

                    </text-field>

                    <text-field :label="trans('google_secret_key')" :value="google_secret_key"
                                :onChange="onChange" name="google_secret_key" type="text" classname="col-sm-6"
                                :required="true">
                    </text-field>
                </div>
            </div>  

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit(true,'/api/admin/recaptcha/enable', {
                    google_site_key : this.google_site_key,
                    google_secret_key: this.google_secret_key})"><i
                    :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
                    &nbsp;                    
                    <button class="btn btn-danger" @click="onSubmit(false, '/api/admin/recaptcha/reset',{clear:1})"><i
                    :class="iconUndo"></i>&nbsp;&nbsp;{{trans('Reset')}}</button>
            </div>
        </div>
    </div>


        <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans('agora-invoicing-integration')}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('agora_invoicing_url')" :value="agora_invoicing_url" :onChange="onChange"
                                name="agora_invoicing_url" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>
            </div>  

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit(true)"><i
                    :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
                    &nbsp;                    
                    <button class="btn btn-danger" @click="onSubmit()"><i
                    :class="iconUndo"></i>&nbsp;&nbsp;{{trans('Reset')}}</button>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

import { successHandler, errorHandler } from '../../helpers/responseHandler';

import { getIdFromUrl } from '../../helpers/extraLogics';

import { validateConfigGenerator } from "../../helpers/validator/validateConfigGenerator.js"

import moment from 'moment'

import TextField from "../../components/Reusable/FormField/TextField.vue";

import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

import ResponseModal from "../../components/Reusable/ResponseModal.vue";

export default {

    name: 'google-recaptcha',

    data() {

        return {

            title: 'google-recaptcha-setting',

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

        }
    },

    beforeMount() {
        this.getProducts();
    },

    methods: {

        getProducts() {

            this.loading = true;

            this.hasDataPopulated = false;


            axios.get('/api/admin/getRecaptcha').then(res => {

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

            if (clear) {

                this.loading = true

                await axios.post(url, formData).then((res) => {

                    successHandler(res, 'google-recaptcha');

                    this.loading = false;

                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'google-recaptcha');
                });
            }
            else{
                 this.loading = true

                const formData = {
                    clear: 1
                }

                await axios.post(url, formData).then((res) => {

                    successHandler(res, 'google-recaptcha');

                    this.loading = false;

                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'google-recaptcha');
                });
            }
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

    }
}
</script>

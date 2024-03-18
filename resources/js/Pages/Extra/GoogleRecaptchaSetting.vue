<template>

    <div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="configuration" />

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

                <button class="btn btn-primary" @click="onSubmit()"><i
                    :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
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

            btnName: 'save',

            hasDataPopulated: false,

            loading: false,

            apiEndpoint: '',

            moment: moment,

            responseData: '',

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

                this.products = res.data.data.map(data => {

                    return {
                        name: data.product_title,
                        value: data.product_id
                    };
                })

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

            if (this.isValid()) {

                this.loading = true

                const formData = {

                }

                await axios.post("/api/admin/recaptcha/enable", formData).then((res) => {

                    successHandler(res, 'google-recaptcha');

                    this.responseData = res.data.replaceAll('<br />\r\n', "")

                    this.showModal = true

                    this.loading = false;

                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'google-recaptcha');
                });
            }
        }
    },

    components: {

        "text-field": TextField,

    }
}
</script>

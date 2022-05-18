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

                    <dynamic-select :label="trans('auto_php_licenser')" :multiple="false"
                                    classname="col-sm-6" :strlength="35"
                                    :required="false" name="auto_php_licenser" :elements="autoPhpLicenser" :value="autoPhpLicenserType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('evanto_api_token')" :value="evantoApiToken" :onChange="onChange" name="evanto_api_token" type="text" classname="col-sm-6">

                    </text-field>
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

import { mapGetters } from 'vuex';

import moment from 'moment'

export default {

    name : 'Advanced Settings',

    data() {

        return {

            title : 'advanced_settings',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment : moment,

            autoPhpLicenser: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            autoPhpLicenserType: null,

            evantoApiToken : null
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);

        this.loadData();
    },

    computed : {

        ...mapGetters(['getApiKey'])
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

        getProducts() {
        },

        getClients() {
        },

        getValues(){
        },

        getInitialValues(){
        },

        updateStatesWithData(){
        },

        isValid() {
        },

        onChange() {
        },

        generateCode() {
        },

        onSubmit(){

        }
    },

    components : {

        "text-field": require("components/Reusable/FormField/TextField").default,

        "number-field": require("components/Reusable/FormField/NumberField").default,

        "static-select": require("components/Reusable/FormField/StaticSelect").default,

        "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,

        "radio-button": require("components/Reusable/FormField/RadioButton").default,

        "date-picker": require("components/Reusable/FormField/DateTimePicker").default
    }
}
</script>

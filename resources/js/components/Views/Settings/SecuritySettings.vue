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

                    <dynamic-select :label="trans('whitelisted_access')" :multiple="false"
                                     classname="col-sm-6" :strlength="35">
                    </dynamic-select>

                    <text-field :label="trans('whitelisted_ip')" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('banned_hosts')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>

                    <text-field :label="trans('message_for_banned_hosts')" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('autoban_failed_login_hosts')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('autoban_failed_licensing_hosts')" :multiple="true" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('forget_failed_attempts')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>

                    <text-field :label="trans('minimum_password_length')" type="number" classname="col-sm-6">

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

    name : 'Security Settings',

    data() {

        return {

            title : 'security_settings',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment:moment
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);

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

        getProducts() {
        },

        getClients() {
        },

        getValues(){
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

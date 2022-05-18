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

                    <dynamic-select :label="trans('whitelisted_access')"  classname="col-sm-6" :strlength="35"
                                    :required="false" name="whitelisted_access" :elements="whitelistedAccess" :value="whitelistedAccessType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('whitelisted_ip')" :value="whiteListedIp" :onChange="onChange" name="whitelisted_ip" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('banned_hosts')" :multiple="false"  classname="col-sm-6" :strlength="35"
                                    :required="false" name="banned_hosts" :elements="bannedHosts" :value="bannedHostsType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('message_for_banned_hosts')" :value="messageBannedHosts" :onChange="onChange" name="message_for_banned_hosts" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('autoban_failed_login_hosts')"  classname="col-sm-6" :strlength="35"
                                    :required="false" name="autoban_failed_login_hosts" :elements="autobanFailedLogin" :value="autobanFailedLoginOptions" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('autoban_failed_licensing_hosts')"  classname="col-sm-6" :strlength="35"
                                    :required="false" name="autoban_failed_licensing_hosts" :elements="autobanFailedLicensing" :value="autobanFailedLicensingOptions" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('forget_failed_attempts')"  classname="col-sm-6" :strlength="35"
                                    :required="false" name="forget_failed_attempts" :elements="ForgetFailedAttempts" :value="ForgetFailedAttemptsOptions" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('minimum_password_length')" :value="minPasswordLength" :onChange="onChange" name="minimum_password_length" type="number" classname="col-sm-6">

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

            moment : moment,

            whitelistedAccess: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            whitelistedAccessType: null,

            whiteListedIp : null,

            bannedHosts: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            bannedHostsType: null,

            messageBannedHosts : null,

            autobanFailedLogin: [
                {name: 'Disabled', value: 'disabled'},
                {name: '1 Attempt', value: '1'},
                {name: '2 Attempts', value: '2'},
                {name: '3 Attempts', value: '3'},
                {name: '4 Attempts', value: '4'},
                {name: '5 Attempts', value: '5'},
                {name: '6 Attempts', value: '6'},
                {name: '7 Attempts', value: '7'},
                {name: '8 Attempts', value: '8'},
                {name: '9 Attempts', value: '9'},
                {name: '10 Attempts', value: '10'}
            ],
            autobanFailedLoginOptions: null,

            autobanFailedLicensing: [
                {name: 'Disabled', value: 'disabled'},
                {name: '1 Attempt', value: '1'},
                {name: '2 Attempts', value: '2'},
                {name: '3 Attempts', value: '3'},
                {name: '4 Attempts', value: '4'},
                {name: '5 Attempts', value: '5'},
                {name: '6 Attempts', value: '6'},
                {name: '7 Attempts', value: '7'},
                {name: '8 Attempts', value: '8'},
                {name: '9 Attempts', value: '9'},
                {name: '10 Attempts', value: '10'}
            ],
            autobanFailedLicensingOptions: null,

            ForgetFailedAttempts: [
                {name: 'Disabled', value: 'disabled'},
                {name: '7 Days', value: '2'},
                {name: '14 Days', value: '3'},
                {name: '30 Days', value: '4'},
                {name: '60 Days', value: '5'},
                {name: '90 Days', value: '6'},
                {name: '180 Days', value: '2'},
                {name: '365 Days', value: '3'}
            ],
            ForgetFailedAttemptsOptions: null,

            minPasswordLength : null
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

        onChange(){

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

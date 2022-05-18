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

                    <dynamic-select :label="trans('auto_system_cleanup')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="auto_system_cleanup" :elements="autoSystemCleanup" :value="autoSystemCleanupType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('remove_callbacks_older_than')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="remove_callbacks_older_than" :elements="removeOlderCallbacks" :value="removeOlderCallbacksOptions" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('remove_license_reports')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="remove_license_reports" :elements="removeLicenseReports" :value="removeLicenseReportsOptions" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('remove_system_reports')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="remove_system_reports" :elements="removeSystemReports" :value="removeSystemReportsOptions" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('remove_licenses_cancelled')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="remove_licenses_cancelled" :elements="removeLicenseCancelled" :value="removeLicenseCancelledType" :onChange="onChange">
                    </dynamic-select>
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

    name : 'Syatem Cleanup Settings',

    data() {

        return {

            title : 'system_cleanup_settings',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment : moment,

            autoSystemCleanup: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            autoSystemCleanupType: null,

            removeOlderCallbacks: [
                {name: 'Disabled', value: 'disabled'},
                {name: '7 Days', value: '1'},
                {name: '14 Days', value: '2'},
                {name: '30 Days', value: '3'},
                {name: '60 Days', value: '4'},
                {name: '90 Days', value: '5'},
                {name: '180 Days', value: '6'},
                {name: '365 Days', value: '7'}
            ],
            removeOlderCallbacksOptions: null,

            removeLicenseReports: [
                {name: 'Disabled', value: 'disabled'},
                {name: '7 Days', value: '1'},
                {name: '14 Days', value: '2'},
                {name: '30 Days', value: '3'},
                {name: '60 Days', value: '4'},
                {name: '90 Days', value: '5'},
                {name: '180 Days', value: '6'},
                {name: '365 Days', value: '7'}
            ],
            removeLicenseReportsOptions: null,

            removeSystemReports: [
                {name: 'Disabled', value: 'disabled'},
                {name: '7 Days', value: '1'},
                {name: '14 Days', value: '2'},
                {name: '30 Days', value: '3'},
                {name: '60 Days', value: '4'},
                {name: '90 Days', value: '5'},
                {name: '180 Days', value: '6'},
                {name: '365 Days', value: '7'}
            ],
            removeSystemReportsOptions: null,

            removeLicenseCancelled: [
                {name: 'Enabled', value: 'enabled'},
                {name: 'Disabled', value: 'disabled'}
            ],
            removeLicenseCancelledType: null,
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
    }
}
</script>

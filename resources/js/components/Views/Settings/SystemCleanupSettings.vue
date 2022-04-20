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

                    <dynamic-select :label="trans('auto_system_cleanup')" :multiple="false" classname="col-sm-6" :strlength="35">
                    </dynamic-select>

                    <dynamic-select :label="trans('remove_callbacks_older_than')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('remove_license_reports')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('remove_system_reports')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('remove_licenses_cancelled')" :multiple="false" classname="col-sm-6" :onChange="onChange" :strlength="35"
                                    :required="false">
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

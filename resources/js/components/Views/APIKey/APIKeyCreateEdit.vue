<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Add new API key. Enter unique API secret, select permissions, and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('api_secret')" type="text" classname="col-sm-6"
                                :showNewButton="client_id ? false : true"
                                newBtnName="generate"
                                :onNewButtonClick="generateCode">

                    </text-field>

                    <text-field :label="trans('api_ip')" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_products')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_products')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_clients')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_clients')" :multiple="true" classname="col-sm-6" :strlength="35"
                                    :required="false" :taggable="true">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_licenses')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_licenses')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_installations')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_use_search')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="trans('api_key_status')" :multiple="false" classname="col-sm-6" :strlength="35"
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

    name : 'Add API ',

    data() {

        return {

            title : 'add_new_api_key',

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

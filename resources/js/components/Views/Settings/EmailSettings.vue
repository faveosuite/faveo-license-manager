<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Configure reminder email settings, enable and disable individual options.</p><br>

                <p><b>Attention</b>: reminder emails will only be sent to personal (email-based) license owners who have their email addresses set.</p>
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

                    <text-field :label="trans('from_name')" type="text" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('from_address')" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('send_copy_sender')" :multiple="false"
                                    classname="col-sm-6" :strlength="35" :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('expiring_license_reminder')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('expiring_updates_reminder')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('expiring_support_reminder')" :multiple="true" classname="col-sm-6"
                                    :strlength="35" :required="false">
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

    name : 'Email Settings',

    data() {

        return {

            title : 'email_settings',

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

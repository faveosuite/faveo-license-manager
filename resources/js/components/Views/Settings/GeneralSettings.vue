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

                    <dynamic-select :label="trans('smart_reports')" :multiple="false"
                                     classname="col-sm-6" :strlength="35">
                    </dynamic-select>

                    <dynamic-select :label="trans('smart_tables')" :multiple="false"
                                    classname="col-sm-6" :strlength="35" :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('records_per_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35" :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('records_on_index_page')" :multiple="false"
                                    classname="col-sm-6" :strlength="35" :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('search_result_limit')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('archive_older_records')" :multiple="true"
                                    classname="col-sm-6" :strlength="35" :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('timezone')" :multiple="false"
                                    classname="col-sm-6" :strlength="35" :required="false">
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

import moment from 'moment'

export default {

    name : 'General Settings',

    data() {

        return {

            title : 'general_settings',

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

        onSubmit(){

        }
    },

    components : {

        "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
    }
}
</script>

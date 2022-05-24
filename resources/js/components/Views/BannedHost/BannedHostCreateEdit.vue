<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Add new banned host to be blocked from using Auto PHP Licenser. Enter IP address and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="banned-hosts"/>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('ip_address')" :value="ipAddress" :onChange="onChange" name="ip_address" type="text" classname="col-sm-6">

                    </text-field>

                    <text-field :label="trans('comments')" type="text" :value="comments" :onChange="onChange" name="comments" classname="col-sm-6">

                    </text-field>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-default" @click="onSubmit"><i :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

import { successHandler, errorHandler } from 'helpers/responseHandler';

import  { getIdFromUrl } from 'helpers/extraLogics';

import { validateLicenseSettings } from "helpers/validator/licenseValidation.js";

import { mapGetters } from 'vuex';

import moment from 'moment'

export default {

    name : 'Add New Banned Host',

    data() {

        return {

            title : 'add_new_banned_host',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment : moment,

            ipAddress : null,

            comments : null
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

        onChange(value, name) {
            if(name === 'ip_address'){
                this.ipAddress = value
            }
            else if(name === 'comments') {
                this.comments = value
            }
        },

        generateCode() {
        },

        onSubmit(){
            const apiKeySecret= this.$store.getters.getApiKey
            if(this.ipAddress && apiKeySecret) {
                const formData = {
                    banned_host_ip: this.ipAddress,
                    banned_host_comments: this.comments,
                    api_key_secret: apiKeySecret
                }
                axios.post("/api/admin/bannedHosts/add",formData).then((res) => {

                    this.loading = false;
                    successHandler(res,'banned-hosts');

                    setTimeout(()=>{
                    
                        this.$router.push('/banned-hosts/list');
                    
                    },2000);

                }).catch((err) => {
                    this.loading = false;
                    errorHandler(err,'banned-hosts');
                });
            }
        }
    },

    components : {

        "text-field": require("components/Reusable/FormField/TextField").default,
    }
}
</script>

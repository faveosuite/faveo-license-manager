<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Automatically generate settings for apl_core_configuration.php file. Select product to be licensed, license verification period, license storage options, and click the 'Submit' button. Once configuration is generated, copy/paste its content to your apl_core_configuration.php file.</p>
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

                    <dynamic-select :label="trans('product')" :elements="productOptions" :multiple="false" classname="col-sm-6" :strlength="35">
                    </dynamic-select>

                    <text-field :label="trans('license_verification_period')" :value="license_verification_period" name="license_verification_period" :onchange="onChange" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('license_storage_type')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <text-field :label="trans('license_file_location')" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="trans('mysql_tablename')" type="text" classname="col-sm-6">

                    </text-field>

                    <dynamic-select :label="trans('delete_cancelled_license')" :multiple="true" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('delete_cracked_license')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('god_mode')" :multiple="false" classname="col-sm-6" :strlength="35"
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

    name : 'Configuration Generator',

    data() {

        return {

            title : 'configuration_generator',

            iconClass : 'fas fa-save',

            btnName : 'save',

            hasDataPopulated : false,

            loading : false,

            apiEndpoint : '',

            moment:moment,

            productOptions: [],

            license_verification_period : ''
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

            axios.get('/api/admin/viewproducts').then(res=>{

                this.productOptions =  res.data.data.map(data=>{

                    data.name = data.product_title;

                    data.id = data.product_id;

                    return data;
                })
            });

            return this.productOptions
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

            this[name] = value ? value : '';

            if(name === 'client_id') {

                if(value){ this.license_code = '' }
            }
        },

        onSubmit(){

            if(this.isValid()){

                this.loading = true

                const data = {};

                data['product_id'] = this.product_id ? this.product_id.id : '';

                data['license_status'] = this.license_status ? 1 : 0;

                data['license_require_domain'] = this.license_require_domain ? 1 : 0;

                if(this.license_verification_period){ data['license_verification_period'] = this.license_verification_period; }

                data['license_ip'] = this.license_ip;

                data['license_domain'] = this.license_domain.toString();

                if(this.license_limit) { data['license_limit'] = this.license_limit; }

                data['license_comments'] = this.license_comments;

                if(this.license_expire_date){
                    data['license_expire_date'] = moment(this.license_expire_date).format("YYYY-MM-DD");
                }

                if(this.license_updates_date){
                    data['license_updates_date'] = moment(this.license_updates_date).format("YYYY-MM-DD");
                }

                if(this.license_support_date){
                    data['license_support_date'] = moment(this.license_support_date).format("YYYY-MM-DD");
                }

                if(!this.client_id){

                    data['license_code'] = this.license_code;
                }

                if(!this.license_code){

                    data['client_id'] = this.client_id ? this.client_id.id : '';
                }

                axios.post(this.apiEndpoint, data).then(res => {

                    this.loading = false

                    successHandler(res,'tools')

                    if(!this.license_id){

                        setTimeout(()=>{

                            this.$router.push('/tools')

                        },2000)

                    } else {

                        this.getInitialValues(this.license_id)
                    }

                }).catch(err => {

                    this.loading = false

                    errorHandler(err,'tools')
                });
            }
        }
    },

    components : {

        "text-field": require("components/Reusable/FormField/TextField").default,

        "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
    }
}
</script>

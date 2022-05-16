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

                    <text-field :label="trans('license_verification_period')" :value="License_Verification_Period" name="License_Verification_Period" :onchange="onChange" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('license_storage_type')" :elements="License_Storage_type" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <text-field :label="trans('license_file_location')" :value="Database_License_File_Location" name="Database_License_File_Location" :onchange="onChange" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="trans('mysql_tablename')" :value="MySQL_Table_Name" name="MySQL_Table_Name" :onchange="onChange" type="text" classname="col-sm-6">

                    </text-field>

                    <dynamic-select :label="trans('delete_cancelled_license')" :elements="Delete_Cancelled_License" :multiple="true" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('delete_cracked_license')" :elements="Delete_Cracked_License" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false">
                    </dynamic-select>

                    <dynamic-select :label="trans('god_mode')" :elements="God_Mode" :multiple="false" classname="col-sm-6" :strlength="35"
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

            productOptions : [],

            License_Verification_Period : '',

            License_Storage_type : '',

            MySQL_Table_Name : '',

            Database_License_File_Location : '',

            Delete_Cancelled_License : '',

            Delete_Cracked_License : '',

            God_Mode : '',
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

                data['License_Storage_type'] = this.License_Storage_type;

                data['license_require_domain'] = this.license_require_domain;

                data['Database_License_File_Location'] = this.Database_License_File_Location;

                data['Delete_Cancelled_License'] = this.Delete_Cancelled_License;

                data['Delete_Cracked_License'] = this.Delete_Cracked_License;

                data['God_Mode'] = this.God_Mode;

                if(this.License_Verification_Period){ data['License_Verification_Period'] = this.License_Verification_Period; }

                axios.post(this.apiEndpoint, data).then(res => {

                    this.loading = false

                    successHandler(res,'tools')

                    if(!this.config){

                        setTimeout(()=>{

                            this.$router.push('/tools')

                        },2000)

                    } else {

                        this.getInitialValues(this.config)
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

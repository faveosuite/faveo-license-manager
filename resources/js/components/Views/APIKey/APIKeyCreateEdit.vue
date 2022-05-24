<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Add new API key. Enter unique API secret, select permissions, and click the 'Submit' button.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="api_keys"/>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="trans('api_secret')" type="text" classname="col-sm-6"
                                :showNewButton="apiSecret ? false : true"
                                newBtnName="generate"
                                :onNewButtonClick="generateCode"
                                name="api_secret" 
                                :value="apiSecret"
						        :onChange="onChange"
                                >

                    </text-field>

                    <text-field :label="trans('api_ip')" :value="apiIp" :onChange="onChange" name="api_ip" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_products')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_add_products" :elements="addProductPermission" :value="addPermissionType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_products')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_edit_products" :elements="editProductPermission" :value="editPermissionType" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_clients')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_add_clients" :elements="addClientPermission" :value="addClientPermissionType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_clients')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_edit_clients" :elements="editClientPermission" :value="editClientPermissionType" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_licenses')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_add_licenses" :elements="addLicensePermission" :value="addLicensePermissionType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_edit_licenses')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_edit_licenses" :elements="editLicensePermission" :value="editLicensePermissionType" :onChange="onChange">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="trans('permissions_to_add_installations')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_add_installations" :elements="addInstallationsPermission" :value="addInstallationsPermissionType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('permissions_to_use_search')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="permissions_to_use_search" :elements="useSearchPermission" :value="useSearchPermissionType" :onChange="onChange">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="trans('api_key_status')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="false" name="api_key_status" :elements="apiKeyStatus" :value="apiKeyStatusType" :onChange="onChange">
                    </dynamic-select>

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

            moment : moment,

            apiIp : null,

            apiSecret: null,

            editProductPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            editPermissionType: null,

            addProductPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            addPermissionType: null,

            addClientPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            addClientPermissionType: null,

            editClientPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            editClientPermissionType: null,

            addLicensePermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            addLicensePermissionType: null,

            editLicensePermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            editLicensePermissionType: null,

            addInstallationsPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            addInstallationsPermissionType: null,

            useSearchPermission: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            useSearchPermissionType: null,

            apiKeyStatus: [
                {name: 'Active', value: 1},
                {name: 'Inactive', value: 0}
            ],
            apiKeyStatusType: null,
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

        generateCode() {

            var a = ''

            var n = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

            for (var e = 1; e <= 16; e++) {

                a += n.charAt(Math.floor(Math.random() * n.length));

                if (e % 4 == 0 && e != 16) {

                    a += ''
                }
            }

            this.apiSecret = a;
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
            if(name === 'api_key'){
                this.apiSecret = value
            }
            else if(name === 'api_ip'){
                this.apiIp = value
            }
            else if(name === 'permissions_to_add_products'){
                this.addPermissionType = value
            }
            else if(name === 'permissions_to_edit_products'){
                this.editPermissionType = value
            }
            else if(name === 'permissions_to_add_clients'){
                this.addClientPermissionType = value
            }
            else if(name === 'permissions_to_edit_clients'){
                this.editClientPermissionType = value
            }
            else if(name === 'permissions_to_add_licenses'){
                this.addLicensePermissionType = value
            }
            else if(name === 'permissions_to_edit_licenses'){
                this.editLicensePermissionType = value
            }
            else if(name === 'permissions_to_add_installations'){
                this.addInstallationsPermissionType = value
            }
            else if(name === 'permissions_to_use_search'){
                this.useSearchPermissionType = value
            }
            else if(name === 'api_key_status'){
                this.apiKeyStatusType = value
            }
        },

        onSubmit(){
            const formData = {
                api_key_secret: this.apiSecret,
                api_key_ip:this.apiIp,
                api_key_clients_add: this.addClientPermissionType ?this.addClientPermissionType.value: null,
                api_key_clients_edit:this.editClientPermissionType ?this.editClientPermissionType.value: null,
                api_key_licenses_add:this.addLicensePermissionType ?this.addLicensePermissionType.value: null,
                api_key_licenses_edit:this.editLicensePermissionType ?this.editLicensePermissionType.value: null,
                api_key_products_add:this.addPermissionType ?this.addPermissionType.value: null,
                api_key_products_edit:this.editPermissionType ?this.editPermissionType.value: null,
                api_key_installations_edit:this.addInstallationsPermissionType ?this.addInstallationsPermissionType.value: null,
                api_key_search:this.useSearchPermissionType ?this.useSearchPermissionType.value: null,
                api_key_status:this.apiKeyStatusType ?this.apiKeyStatusType.value: null,
            }

            axios.post("/api/admin/addnewapi",formData).then((res) => {

                this.loading = false;

                successHandler(res,'api_keys');

                setTimeout(()=>{

                this.$router.push('/apikeys/list');

                },2000);

                }).catch((err) => {

                this.loading = false;

                errorHandler(err,'api_keys');
                });
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

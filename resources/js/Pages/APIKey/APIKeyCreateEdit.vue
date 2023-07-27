<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>Add new API key. Enter unique API secret, select permissions, and click the 'Submit' button.</span>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="api_keys" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('api_secret')" type="text" classname="col-sm-6"
                        :showNewButton="api_key_secret ? false : true" newBtnName="generate" :onNewButtonClick="generateCode"
                        name="api_key_secret" :value="api_key_secret" :onChange="onChange" :required="true">

                    </text-field>

                    <text-field :label="lang('api_ip')" :value="api_key_ip" :onChange="onChange" name="api_key_ip" type="text"
                        classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('permissions_to_add_products')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_products_add"
                        :elements="addProductPermission" :value="api_key_products_add" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('permissions_to_edit_products')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_products_edit"
                        :elements="editProductPermission" :value="api_key_products_edit" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('permissions_to_add_clients')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_clients_add"
                        :elements="addClientPermission" :value="api_key_clients_add" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('permissions_to_edit_clients')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_clients_edit"
                        :elements="editClientPermission" :value="api_key_clients_edit" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('permissions_to_add_licenses')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_licenses_add"
                        :elements="addLicensePermission" :value="api_key_licenses_add" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('permissions_to_edit_licenses')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_licenses_edit"
                        :elements="editLicensePermission" :value="api_key_licenses_edit" :onChange="onChange">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="lang('permissions_to_add_installations')" :multiple="false"
                        classname="col-sm-6" :strlength="35" :required="true" name="api_key_installations_edit"
                        :elements="addInstallationsPermission" :value="api_key_installations_edit"
                        :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('permissions_to_use_search')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_search"
                        :elements="useSearchPermission" :value="api_key_search" :onChange="onChange">
                    </dynamic-select>

                </div>

                <div class="row">

                    <dynamic-select :label="lang('api_key_status')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="api_key_status" :elements="apiKeyStatus"
                        :value="api_key_status" :onChange="onChange">
                    </dynamic-select>


                    <text-field :label="lang('api_key_description')" :value="api_key_description" type="textarea"
                                name="api_key_description" :onChange="onChange" classname="col-sm-6">
                    </text-field>

                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit"><i
                        :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios'

    import { successHandler, errorHandler } from '../../helpers/responseHandler';

    import { getIdFromUrl, generateRandomString } from '../../helpers/extraLogics';

    import { ApiKeysValidation } from "../../helpers/validator/ApiKeysValidation.js";

    import moment from 'moment'

    import TextField from "../../components/Reusable/FormField/TextField.vue";

    import NumberField from "../../components/Reusable/FormField/NumberField.vue";

    import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

    import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

    export default {

        name: 'add-api',

        data() {

            return {

                title: 'add_new_api_key',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                api_key_ip: null,

                api_key_secret: '',

                api_key_id: '',

                editProductPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_products_edit: '',

                addProductPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_products_add: '',

                addClientPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_clients_add: '',

                editClientPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_clients_edit: '',

                addLicensePermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_licenses_add: '',

                editLicensePermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_licenses_edit: '',

                addInstallationsPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_installations_edit: '',

                useSearchPermission: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_search: '',

                api_key_description: '',

                apiKeyStatus: [
                    { name: 'Active', value: 1 },
                    { name: 'Inactive', value: 0 }
                ],

                api_key_status: '',
            }
        },

        beforeMount() {

            const path = window.location.pathname

            this.getValues(path);

        },

        methods: {

            getValues(path) {

                const apiKeyId = getIdFromUrl(path)

                if (path.indexOf('edit') >= 0) {

                    this.title = 'edit_api_key'

                    this.iconClass = 'fas fa-sync'

                    this.btnName = 'update'

                    this.hasDataPopulated = false

                    this.getInitialValues(apiKeyId);

                    this.api_key_id = apiKeyId;

                    this.apiEndpoint = `/api/admin/editnewapi/${apiKeyId}`;

                } else {

                    this.loading = false;

                    this.hasDataPopulated = true;

                    this.apiEndpoint = '/api/admin/addnewapi';
                }
            },

            getInitialValues(id) {

                this.loading = true

                axios.get(`/api/admin/viewApiKeys/${id}`).then(res => {

                    this.loading = false;

                    this.hasDataPopulated = true

                    this.updateStatesWithData(res.data.data.api_key);

                }).catch(error => {

                    this.loading = false;
                });
            },

            updateStatesWithData(data) {

                if ('api_key_secret' in data) {
                    this.api_key_secret = data.api_key_secret
                }
                if ('api_key_ip' in data) {
                    this.api_key_ip = data.api_key_ip
                }
                if ('api_key_products_add' in data) {
                    this.api_key_products_add = this.findOption('addProductPermission', data.api_key_products_add)
                }
                if ('api_key_products_edit' in data) {
                    this.api_key_products_edit = this.findOption('editProductPermission', data.api_key_products_edit)
                }
                if ('api_key_clients_add' in data) {
                    this.api_key_clients_add = this.findOption('addClientPermission', data.api_key_clients_add)
                }
                if ('api_key_clients_edit' in data) {
                    this.api_key_clients_edit = this.findOption('editClientPermission', data.api_key_clients_edit)
                }
                if ('api_key_licenses_add' in data) {
                    this.api_key_licenses_add = this.findOption('addLicensePermission', data.api_key_licenses_add)
                }
                if ('api_key_licenses_edit' in data) {
                    this.api_key_licenses_edit = this.findOption('editLicensePermission', data.api_key_licenses_edit)
                }
                if ('api_key_installations_edit' in data) {
                    this.api_key_installations_edit = this.findOption('addInstallationsPermission', data.api_key_installations_edit)
                }
                if ('api_key_search' in data) {
                    this.api_key_search = this.findOption('useSearchPermission', data.api_key_search)
                }
                if ('api_key_status' in data) {
                    this.api_key_status = this.findOption('apiKeyStatus', data.api_key_status)
                }
            },

            isValid() {

                const { errors, isValid } = ApiKeysValidation(this.$data);

                return isValid;
            },

            generateCode() {
                this.api_key_secret = generateRandomString(16);
            },

            findOption(options, value) {
                return this[options].find((option) => option.value === value)
            },
            onChange(value, name) {
                if (name === 'api_key_secret') {
                    this.api_key_secret = value
                }
                else if (name === 'api_key_ip') {
                    this.api_key_ip = value
                }
                else if (name === 'api_key_products_add') {
                    this.api_key_products_add = value
                }
                else if (name === 'api_key_products_edit') {
                    this.api_key_products_edit = value
                }
                else if (name === 'api_key_clients_add') {
                    this.api_key_clients_add = value
                }
                else if (name === 'api_key_clients_edit') {
                    this.api_key_clients_edit = value
                }
                else if (name === 'api_key_licenses_add') {
                    this.api_key_licenses_add = value
                }
                else if (name === 'api_key_licenses_edit') {
                    this.api_key_licenses_edit = value
                }
                else if (name === 'api_key_installations_edit') {
                    this.api_key_installations_edit = value
                }
                else if (name === 'api_key_search') {
                    this.api_key_search = value
                }
                else if (name === 'api_key_status') {
                    this.api_key_status = value
                }
            },

            onSubmit() {

                if (this.isValid()) {

                    this.loading = true

                    const formData = {
                        api_key_description: this.api_key_description,

                        api_key_secret: this.api_key_secret,

                        api_key_ip: this.api_key_ip,

                        api_key_id: this.api_key_id,

                        api_key_clients_add: this.api_key_clients_add ? this.api_key_clients_add.value : null,

                        api_key_clients_edit: this.api_key_clients_edit ? this.api_key_clients_edit.value : null,

                        api_key_licenses_add: this.api_key_licenses_add ? this.api_key_licenses_add.value : null,

                        api_key_licenses_edit: this.api_key_licenses_edit ? this.api_key_licenses_edit.value : null,

                        api_key_products_add: this.api_key_products_add ? this.api_key_products_add.value : null,

                        api_key_products_edit: this.api_key_products_edit ? this.api_key_products_edit.value : null,

                        api_key_installations_edit: this.api_key_installations_edit ? this.api_key_installations_edit.value : null,

                        api_key_search: this.api_key_search ? this.api_key_search.value : null,

                        api_key_status: this.api_key_status ? this.api_key_status.value : null,
                    }

                    axios.post(this.apiEndpoint, formData).then((res) => {

                        this.loading = false;

                        console.log('this',this.api_key_description),


                            successHandler(res, 'api_keys');

                        if (!this.api_key_id) {

                            setTimeout(() => {

                                this.$router.push('/apikeys/list')

                            }, 2000)

                        } else {

                            this.getInitialValues(this.api_key_id)
                        }

                    }).catch((err) => {

                        this.loading = false;

                        errorHandler(err, 'api_keys');
                    });
                }
            },



        },

        components: {

            "text-field": TextField,

            "number-field": NumberField,

            "static-select": StaticSelect,

            "dynamic-select": DynamicSelect,
        }
    }
</script>

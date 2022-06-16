<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Automatically generate settings for apl_core_configuration.php file. Select product to be licensed,
                license verification period, license storage options, and click the 'Submit' button. Once configuration
                is generated, copy/paste its content to your apl_core_configuration.php file.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="configuration" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{trans(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <dynamic-select :label="trans('product')" :multiple="false" classname="col-sm-6" :strlength="35"
                        :value="selectedProduct" :onChange="onChange" :elements="products" name="product"
                        :required="true">
                    </dynamic-select>

                    <text-field :label="trans('license_verification_period')" :value="verificationPeriod"
                        :onChange="onChange" name="license_verification_period" type="text" classname="col-sm-6"
                        :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('license_storage_type')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="license_storage_type" :elements="storageTypes"
                        :value="selectedStorageType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('license_file_location')" :value="fileLocation" :onChange="onChange"
                        name="license_file_location" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="trans('mysql_tablename')" :value="mysqlTablename" :onChange="onChange"
                        name="mysql_tablename" type="text" classname="col-sm-6" :required="true">

                    </text-field>

                    <dynamic-select :label="trans('delete_cancelled_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="delete_cancelled_license"
                        :elements="delCancelledLicenceOpt" :value="delCancelledLicence" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('delete_cracked_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="delete_cracked_license" :elements="delCrackedLicenceOpt"
                        :value="delCrackedLicence" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('god_mode')" :multiple="false" classname="col-sm-6" :strlength="35"
                        :required="true" name="god_mode" :elements="godModeOpt" :value="godMode" :onChange="onChange">
                    </dynamic-select>

                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit()"><i
                        :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
            </div>
        </div>

        <transition name="modal">

            <response-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :title="modalTitle"
                :responseData="responseData">

            </response-modal>
        </transition>

    </div>
</template>

<script>

    import axios from 'axios'

    import { successHandler, errorHandler } from 'helpers/responseHandler';

    import { getIdFromUrl } from 'helpers/extraLogics';

    import { validateLicenseSettings } from "helpers/validator/licenseValidation.js";

    import { mapGetters } from 'vuex';

    import moment from 'moment'

    export default {

        name: 'configuration-generator',

        data() {

            return {

                title: 'configuration_generator',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                products: [],

                selectedProduct: null,

                verificationPeriod: null,

                storageTypes: [
                    { name: 'Database', value: 'DATABASE' },
                    { name: 'File', value: 'FILE' }
                ],

                selectedStorageType: null,

                fileLocation: null,

                mysqlTablename: null,

                delCancelledLicenceOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                delCancelledLicence: null,

                delCrackedLicenceOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                delCrackedLicence: null,

                godModeOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                godMode: null,

                showModal: false,

                responseData: '',

                modalTitle: 'Copy Response'
            }
        },

        beforeMount() {

            const path = window.location.pathname

            this.getValues(path);

            this.loadData();
        },

        methods: {

            loadData() {

                this.loading = true;

                this.hasDataPopulated = false;

                Promise.all([this.getProducts()]).then((values) => {

                    [this.productOptions] = values;

                    this.loading = false;

                    this.hasDataPopulated = true;

                }).catch(function (error) {

                    this.loading = false;

                    this.hasDataPopulated = true;
                });
            },

            getProducts() {

                axios.get('/api/admin/viewproducts').then(res => {

                    this.products = res.data.data.map(data => {

                        return {
                            name: data.product_title,
                            value: data.product_id
                        };
                    })
                })
            },

            getValues() {
            },

            onChange(option, name) {

                if (name === 'product') {

                    this.selectedProduct = option

                } else if (name === 'license_storage_type') {

                    this.selectedStorageType = option

                } else if (name === 'delete_cancelled_license') {

                    this.delCancelledLicence = option

                } else if (name === 'delete_cracked_license') {

                    this.delCrackedLicence = option

                } else if (name === 'god_mode') {

                    this.godMode = option

                } else if (name === 'license_verification_period') {

                    this.verificationPeriod = option

                } else if (name === 'license_file_location') {

                    this.fileLocation = option

                } else if (name === 'mysql_tablename') {

                    this.mysqlTablename = option
                }
            },

            onClose() {

                this.showModal = false;
                this.responseData = null;

            },

            async onSubmit() {
                this.loading = true;

                const formData = {

                    product_id: this.selectedProduct ? this.selectedProduct.value : null,

                    License_Verification_Period: this.verificationPeriod,

                    License_Storage_type: this.selectedStorageType ? this.selectedStorageType.value : null,

                    MySQL_Table_Name: this.mysqlTablename,

                    Database_License_File_Location: this.fileLocation,

                    Delete_Cancelled_License: this.delCancelledLicence ? this.delCancelledLicence.value : null,

                    Delete_Cracked_License: this.delCrackedLicence ? this.delCrackedLicence.value : null,

                    God_Mode: this.godMode ? this.godMode.value : null,

                }

                await axios.post("/api/admin/config", formData).then((res) => {

                    this.loading = false;

                    successHandler(res, 'configuration');

                    this.responseData = res
                    this.showModal = true

                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'configuration');
                });
            }
        },

        components: {

            "text-field": require("components/Reusable/FormField/TextField").default,

            "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,

            'response-modal': require('components/Reusable/ResponseModal').default,
        }
    }
</script>
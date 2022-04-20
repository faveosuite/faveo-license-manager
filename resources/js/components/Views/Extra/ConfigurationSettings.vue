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
                        :value="selectedProduct" :onChange="onChange" :elements="products" name="product_id"
                        :required="true">
                    </dynamic-select>

                    <text-field :label="trans('license_verification_period')" :value="verificationPeriod"
                        :onChange="onChange" name="License_Verification_Period" type="text" classname="col-sm-6"
                        :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('license_storage_type')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="License_Storage_type" :elements="storageTypes"
                        :value="selectedStorageType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('license_file_location')" :value="fileLocation" :onChange="onChange"
                        name="Database_License_File_Location" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="trans('mysql_tablename')" :value="mysqlTablename" :onChange="onChange"
                        name="MySQL_Table_Name" type="text" classname="col-sm-6" :required="true">

                    </text-field>

                    <dynamic-select :label="trans('delete_cancelled_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="Delete_Cancelled_License"
                        :elements="delCancelledLicenceOpt" :value="delCancelledLicence" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('delete_cracked_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="Delete_Cracked_License" :elements="delCrackedLicenceOpt"
                        :value="delCrackedLicence" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('god_mode')" :multiple="false" classname="col-sm-6" :strlength="35"
                        :required="true" name="God_Mode" :elements="godModeOpt" :value="godMode" :onChange="onChange">
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
    import { validateConfigGenerator } from "helpers/validator/validateConfigGenerator.js"
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
                } else if (name === 'License_Storage_type') {
                    this.selectedStorageType = option
                } else if (name === 'Delete_Cancelled_License') {
                    this.delCancelledLicence = option
                } else if (name === 'Delete_Cracked_License') {
                    this.delCrackedLicence = option
                } else if (name === 'God_Mode') {
                    this.godMode = option
                } else if (name === 'License_Verification_Period') {
                    this.verificationPeriod = option
                } else if (name === 'Database_License_File_Location') {
                    this.fileLocation = option
                } else if (name === 'MySQL_Table_Name') {
                    this.mysqlTablename = option
                }
            },
            onClose() {
                this.showModal = false;
                this.responseData = null;
            },
            isValid() {
                const { errors, isValid } = validateConfigGenerator(this.$data);
                return isValid;
            },
            async onSubmit() {
                if (this.isValid) {
                    this.loading = true
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
            }
        },
        components: {
            "text-field": require("components/Reusable/FormField/TextField").default,
            "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
            'response-modal': require('components/Reusable/ResponseModal').default,
        }
    }
</script>
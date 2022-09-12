<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>Automatically generate settings for apl_core_configuration.php file. Select product to be licensed,
                license verification period, license storage options, and click the 'Submit' button. Once configuration
                is generated, copy/paste its content to your apl_core_configuration.php file.</span>
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
                        :value="product_id" :onChange="onChange" :elements="products" name="product_id"
                        :required="true">
                    </dynamic-select>

                    <text-field :label="trans('license_verification_period')" :value="License_Verification_Period"
                        :onChange="onChange" name="License_Verification_Period" type="text" classname="col-sm-6"
                        :required="true" placehold="Verification Period should be less than 365">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('license_storage_type')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="License_Storage_type" :elements="storageTypes"
                        :value="License_Storage_type" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="trans('license_file_location')" :value="Database_License_File_Location" :onChange="onChange"
                        name="Database_License_File_Location" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="trans('mysql_tablename')" :value="MySQL_Table_Name" :onChange="onChange"
                        name="MySQL_Table_Name" type="text" classname="col-sm-6" :required="true">

                    </text-field>

                    <dynamic-select :label="trans('delete_cancelled_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="Delete_Cancelled_License"
                        :elements="delCancelledLicenceOpt" :value="Delete_Cancelled_License" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="trans('delete_cracked_license')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="Delete_Cracked_License" :elements="delCrackedLicenceOpt"
                        :value="Delete_Cracked_License" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="trans('god_mode')" :multiple="false" classname="col-sm-6" :strlength="35"
                        :required="true" name="God_Mode" :elements="godModeOpt" :value="God_Mode" :onChange="onChange">
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

    import { successHandler, errorHandler } from '../../helpers/responseHandler';

    import { getIdFromUrl } from '../../helpers/extraLogics';

    import { validateConfigGenerator } from "../../helpers/validator/validateConfigGenerator.js"

    import moment from 'moment'

    import TextField from "../../components/Reusable/FormField/TextField.vue";

    import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

    import ResponseModal from "../../components/Reusable/ResponseModal.vue";

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

                product_id: "",

                License_Verification_Period: "",

                storageTypes: [
                    { name: 'Database', value: 'DATABASE' },
                    { name: 'File', value: 'FILE' }
                ],

                License_Storage_type: "",

                Database_License_File_Location: "",

                MySQL_Table_Name: "",

                delCancelledLicenceOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                Delete_Cancelled_License: "",

                delCrackedLicenceOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                Delete_Cracked_License: "",

                godModeOpt: [
                    { name: 'Yes', value: 'YES' },
                    { name: 'No', value: 'NO' }
                ],

                God_Mode: "",

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

                this[name] = option ? option : '';
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

                if (this.isValid()) {

                    this.loading = true

                    const formData = {

                        product_id: this.product_id ? this.product_id.value : null,

                        License_Verification_Period: this.License_Verification_Period,

                        License_Storage_type: this.License_Storage_type ? this.License_Storage_type.value : null,

                        MySQL_Table_Name: this.MySQL_Table_Name,

                        Database_License_File_Location: this.Database_License_File_Location,

                        Delete_Cancelled_License: this.Delete_Cancelled_License ? this.Delete_Cancelled_License.value : null,

                        Delete_Cracked_License: this.Delete_Cracked_License ? this.Delete_Cracked_License.value : null,

                        God_Mode: this.God_Mode ? this.God_Mode.value : null,

                    }

                    await axios.post("/api/admin/config", formData).then((res) => {

                        this.loading = false;

                        successHandler(res, 'configuration');

                        this.responseData = res.data.replaceAll('<br />\r\n', "")

                        this.showModal = true

                    }).catch((err) => {

                        this.loading = false;

                        errorHandler(err, 'configuration');
                    });
                }
            }
        },

        components: {

            "text-field": TextField,

            "dynamic-select": DynamicSelect,

            'response-modal': ResponseModal,
        }
    }
</script>

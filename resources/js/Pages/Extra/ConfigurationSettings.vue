<template>
  <div class="col-sm-12">
    <div class="alert alert-info">
      <span>
        {{trans("config_gen")}}
      </span>
    </div>

    <div class="row" v-if="loading">
      <custom-loader :duration="4000"></custom-loader>
    </div>

    <alert componentName="configuration" />

    <div class="card card-light" v-if="hasDataPopulated">
      <div class="card-header">
        <h3 class="card-title">{{ trans(title) }}</h3>
      </div>

      <div class="card-body">
        <div class="row">
          <dynamic-select
            :label="trans('product')"
            :multiple="false"
            classname="col-sm-6"
            :strlength="35"
            :value="product_id"
            :onChange="onChange"
            :elements="products"
            name="product_id"
            :required="true"
            :hint="trans('product_for_info')"
          ></dynamic-select>

          <text-field
            :label="trans('license_verification_period')"
            :value="License_Verification_Period"
            :onChange="onChange"
            name="License_Verification_Period"
            type="text"
            classname="col-sm-6"
            :required="true"
            placehold="Verification Period should be less than 365"
            :hint="trans('license_verification_period_info')"
          ></text-field>
        </div>

        <div class="row">
          <dynamic-select
            :label="trans('license_storage_type')"
            :multiple="false"
            classname="col-sm-6"
            :strlength="35"
            :required="true"
            name="License_Storage_type"
            :elements="storageTypes"
            :value="License_Storage_type"
            :onChange="onChange"
            :hint="trans('license_storage_type_info')"
          ></dynamic-select>

          <text-field
            :label="trans('license_file_location')"
            :value="Database_License_File_Location"
            :onChange="onChange"
            name="Database_License_File_Location"
            type="text"
            classname="col-sm-6"
            :required="true"
            :hint="trans('license_file_location_info')"
          ></text-field>
        </div>

        <div class="row">
          <text-field
            :label="trans('mysql_tablename')"
            :value="MySQL_Table_Name"
            :onChange="onChange"
            name="MySQL_Table_Name"
            type="text"
            classname="col-sm-6"
            :required="true"
            :hint="trans('mysql_tablename_info')"
          ></text-field>

          <dynamic-select
            :label="trans('delete_cancelled_license')"
            :multiple="false"
            classname="col-sm-6"
            :strlength="35"
            :required="true"
            name="Delete_Cancelled_License"
            :elements="delCancelledLicenceOpt"
            :value="Delete_Cancelled_License"
            :onChange="onChange"
            :hint="trans('delete_cancelled_license_info')"
          ></dynamic-select>
        </div>

        <div class="row">
          <dynamic-select
            :label="trans('delete_cracked_license')"
            :multiple="false"
            classname="col-sm-6"
            :strlength="35"
            :required="true"
            name="Delete_Cracked_License"
            :elements="delCrackedLicenceOpt"
            :value="Delete_Cracked_License"
            :onChange="onChange"
            :hint="trans('delete_cracked_license_info')"
          ></dynamic-select>

          <dynamic-select
            :label="trans('god_mode')"
            :multiple="false"
            classname="col-sm-6"
            :strlength="35"
            :required="true"
            name="God_Mode"
            :elements="godModeOpt"
            :value="God_Mode"
            :onChange="onChange"
            :hint="trans('god_mode_info')"
          ></dynamic-select>
        </div>
      </div>

      <div class="card-footer">
        <button class="btn btn-primary" @click="onSubmit()">
          <i :class="iconClass"></i>&nbsp;&nbsp;{{ trans(btnName) }}
        </button>
      </div>
    </div>

    <transition name="modal">
      <response-modal
        v-if="showModal"
        :onClose="onClose"
        :showModal="showModal"
        :title="modalTitle"
        :responseData="responseData"
      ></response-modal>
    </transition>
  </div>
</template>

<script>
import axios from 'axios';
import { successHandler, errorHandler } from '../../helpers/responseHandler';
import { getIdFromUrl } from '../../helpers/extraLogics';
import { validateConfigGenerator } from '../../helpers/validator/validateConfigGenerator.js';
import moment from 'moment';
import TextField from '../../components/Reusable/FormField/TextField.vue';
import DynamicSelect from '../../components/Reusable/FormField/DynamicSelect.vue';
import ResponseModal from '../../components/Reusable/ResponseModal.vue';

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
      product_id: '', // Initial value here
      License_Verification_Period: '30', // Initial value here
      storageTypes: [
        { name: 'Database', value: 'DATABASE' },
        { name: 'File', value: 'FILE' },
      ],
      License_Storage_type: { name: 'Database', value: 'DATABASE' }, // Initial value here
      Database_License_File_Location: '/path/to/license', // Initial value here
      MySQL_Table_Name: 'licenses', // Initial value here
      delCancelledLicenceOpt: [
        { name: 'Yes', value: 'YES' },
        { name: 'No', value: 'NO' },
      ],
      Delete_Cancelled_License: { name: 'Yes', value: 'YES' }, // Initial value here
      delCrackedLicenceOpt: [
        { name: 'Yes', value: 'YES' },
        { name: 'No', value: 'NO' },
      ],
      Delete_Cracked_License: { name: 'Yes', value: 'YES' }, // Initial value here
      godModeOpt: [
        { name: 'Yes', value: 'YES' },
        { name: 'No', value: 'NO' },
      ],
      God_Mode: { name: 'No', value: 'NO' }, // Initial value here
      showModal: false,
      responseData: '',
      modalTitle: 'Copy Response',
    };
  },
  beforeMount() {
    const path = window.location.pathname;
    this.getValues(path);
    this.getProducts();
  },
  methods: {
    getProducts() {
      this.loading = true;
      this.hasDataPopulated = false;
      axios
        .get('/api/admin/viewproducts')
        .then((res) => {
          this.products = res.data.data.map((data) => {
            return {
              name: data.product_title,
              value: data.product_id,
            };
          });
          this.loading = false;
          this.hasDataPopulated = true;
        })
        .catch((err) => {
          this.loading = false;
        });
    },
    getValues() {},
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
        this.loading = true;
        const formData = {
          product_id: this.product_id ? this.product_id.value : null,
          License_Verification_Period: this.License_Verification_Period,
          License_Storage_type: this.License_Storage_type
            ? this.License_Storage_type.value
            : null,
          MySQL_Table_Name: this.MySQL_Table_Name,
          Database_License_File_Location: this.Database_License_File_Location,
          Delete_Cancelled_License: this.Delete_Cancelled_License
            ? this.Delete_Cancelled_License.value
            : null,
          Delete_Cracked_License: this.Delete_Cracked_License
            ? this.Delete_Cracked_License.value
            : null,
          God_Mode: this.God_Mode ? this.God_Mode.value : null,
        };
        await axios
          .post('/api/admin/config', formData)
          .then((res) => {
            successHandler(res, 'configuration');
            this.responseData = res.data.replaceAll('<br />\r\n', '');
            this.showModal = true;
            this.loading = false;
          })
          .catch((err) => {
            this.loading = false;
            errorHandler(err, 'configuration');
          });
      }
    },
  },
  components: {
    'text-field': TextField,
    'dynamic-select': DynamicSelect,
    'response-modal': ResponseModal,
  },
};
</script>

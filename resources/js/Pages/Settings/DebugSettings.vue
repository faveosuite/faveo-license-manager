<template>

  <div class="col-sm-12">

     <h1>hjpoiuyf</h1>
  </div>
</template>

<script>

  import axios from 'axios'

  import { successHandler, errorHandler } from '../../helpers/responseHandler';

  import { getIdFromUrl } from '../../helpers/extraLogics';

  import { validateCleanUpSettings } from "../../helpers/validator/validateCleanUpSettings.js";

  import moment from 'moment'

  import TextField from "../../components/Reusable/FormField/TextField.vue";

  import NumberField from "../../components/Reusable/FormField/NumberField.vue";

  import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

  import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

  export default {

      name: 'System-Cleanup-Settings',

      data() {

          return {

              title: 'system_cleanup_settings',

              iconClass: 'fas fa-save',

              btnName: 'save',

              hasDataPopulated: false,

              loading: false,

              apiEndpoint: '',

              moment: moment,

              autoSystemCleanup: [
                  { name: 'Enabled', value: 1 },
                  { name: 'Disabled', value: 0 }
              ],
              autoSystemCleanupType: null,

              removeOlderCallbacks: [],

              removeOlderCallbacksOptions: null,

              removeLicenseReports: [],

              removeLicenseReportsOptions: null,

              removeSystemReports: [],

              removeSystemReportsOptions: null,

              removeLicenseCancelled: [
                  { name: 'Enabled', value: 1 },
                  { name: 'Disabled', value: 0 }
              ],

              removeLicenseCancelledType: null,

          }
      },

      async beforeMount() {

          const path = window.location.pathname

          await this.getCleanUpDropdownOptions()

          this.loadData();
      },

      methods: {

          async loadData() {

              this.loading = true;

              this.hasDataPopulated = false;

              await this.$store.dispatch('fetchSettings');

              this.setFormData()

              this.hasDataPopulated = true;

              this.loading = false;
          },

          async getCleanUpDropdownOptions() {
              this.loading = true;

              return await axios.get("/api/admin/cleanupSettings").then((res) => {
                  const options = res.data

                  if (options['database cleanup callbacks']) {
                      this.removeOlderCallbacks = options['database cleanup callbacks']
                  }
                  if (options['database cleanup reports main']) {
                      this.removeLicenseReports = options['database cleanup reports main']
                  }
                  if (options['database cleanup reports system']) {
                      this.removeSystemReports = options['database cleanup reports system']
                  }
                  if (options['database cleanup licenses']) {
                      this.removeLicenseCancelled = options['database cleanup licenses']
                  }

                  this.loading = false;

              }).catch((err) => {

                  this.loading = false;

              });
          },

          isValid() {

              const { errors, isValid } = validateCleanUpSettings(this.$data);

              return isValid;
          },

          setFormData() {

              const cleanUpSettings = this.$store.getters['getCleanUpSettings']

              if (cleanUpSettings) {

                  this.settingId = cleanUpSettings.SETTING_ID ?? 'new'

                  this.autoSystemCleanupType = this.autoSystemCleanup.find((opt) => {
                      return opt.value === cleanUpSettings.DATABASE_CLEANUP_ENABLED
                  })

                  this.removeOlderCallbacksOptions = this.removeOlderCallbacks.find((opt) => {
                      return opt.value === cleanUpSettings.DATABASE_CLEANUP_CALLBACKS
                  })

                  this.removeLicenseReportsOptions = this.removeLicenseReports.find((opt) => {
                      return opt.value === cleanUpSettings.DATABASE_CLEANUP_REPORTS_MAIN
                  })

                  this.removeSystemReportsOptions = this.removeSystemReports.find((opt) => {
                      return opt.value === cleanUpSettings.DATABASE_CLEANUP_REPORTS_SYSTEM
                  })

                  this.removeLicenseCancelledType = this.removeLicenseCancelled.find((opt) => {
                      return opt.value === cleanUpSettings.DATABASE_CLEANUP_REPORTS_LICENSES
                  })
              }
          },

          onChange(value, name) {
              if (name === 'DATABASE_CLEANUP_ENABLED') {
                  this.autoSystemCleanupType = value
              } else if (name === 'DATABASE_CLEANUP_CALLBACKS') {
                  this.removeOlderCallbacksOptions = value
              } else if (name === 'DATABASE_CLEANUP_REPORTS_MAIN') {
                  this.removeLicenseReportsOptions = value
              } else if (name === 'DATABASE_CLEANUP_REPORTS_SYSTEM') {
                  this.removeSystemReportsOptions = value
              } else if (name === 'DATABASE_CLEANUP_REPORTS_LICENSES') {
                  this.removeLicenseCancelledType = value
              }
          },

          async onSubmit() {

              if (this.isValid()) {

                  this.loading = true
                  const formData = {

                      DATABASE_CLEANUP_ENABLED: this.autoSystemCleanupType ? this.autoSystemCleanupType.value : null,

                      DATABASE_CLEANUP_CALLBACKS: this.removeOlderCallbacksOptions ? this.removeOlderCallbacksOptions.value : null,

                      DATABASE_CLEANUP_REPORTS_MAIN: this.removeLicenseReportsOptions ? this.removeLicenseReportsOptions.value : null,

                      DATABASE_CLEANUP_REPORTS_SYSTEM: this.removeSystemReportsOptions ? this.removeSystemReportsOptions.value : null,

                      DATABASE_CLEANUP_REPORTS_LICENSES: this.removeLicenseCancelledType ? this.removeLicenseCancelledType.value : null,
                  }

                  await axios.post(`/api/admin/cleanupsettings/${this.settingId}`, formData).then(async (res) => {


                      successHandler(res, 'settings');

                      await this.$store.dispatch('fetchSettings');

                      this.loading = false;

                  }).catch((err) => {

                      this.loading = false;

                      errorHandler(err, 'settings');
                  });
              }
          }
      },

      components: {

          "text-field": TextField,

          "number-field": NumberField,

          "static-select": StaticSelect,

          "dynamic-select": DynamicSelect,
      }
  }
</script>

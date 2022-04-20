<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Configure general software settings, enable and disable individual options.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="settings" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <dynamic-select :label="lang('auto_system_cleanup')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="DATABASE_CLEANUP_ENABLED" :elements="autoSystemCleanup"
                        :value="autoSystemCleanupType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('remove_callbacks_older_than')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="DATABASE_CLEANUP_CALLBACKS"
                        :elements="removeOlderCallbacks" :value="removeOlderCallbacksOptions" optionLabel="title"
                        :onChange="onChange">

                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('remove_license_reports')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="DATABASE_CLEANUP_REPORTS_MAIN" :elements="removeLicenseReports"
                        :value="removeLicenseReportsOptions" optionLabel="title" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('remove_system_reports')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="DATABASE_CLEANUP_REPORTS_SYSTEM" :elements="removeSystemReports"
                        :value="removeSystemReportsOptions" optionLabel="title" :onChange="onChange">

                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('remove_licenses_cancelled')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="DATABASE_CLEANUP_REPORTS_LICENSES"
                        :elements="removeLicenseCancelled" optionLabel="title" :value="removeLicenseCancelledType"
                        :onChange="onChange">
                    </dynamic-select>
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
    import { successHandler, errorHandler } from 'helpers/responseHandler';
    import { getIdFromUrl } from 'helpers/extraLogics';
    import { validateCleanUpSettings } from "helpers/validator/validateCleanUpSettings.js";
    import { mapGetters } from 'vuex';
    import moment from 'moment'
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
            "text-field": require("components/Reusable/FormField/TextField").default,
            "number-field": require("components/Reusable/FormField/NumberField").default,
            "static-select": require("components/Reusable/FormField/StaticSelect").default,
            "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,
        }
    }
</script>
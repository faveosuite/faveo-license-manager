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

                    <dynamic-select :label="lang('whitelisted_access')" classname="col-sm-6" :strlength="35"
                        :required="true" name="WHITELISTED_ACCESS" :elements="whitelistedAccess"
                        :value="whitelistedAccessType" :onChange="onChange">
                    </dynamic-select>


                    <dynamic-select :label="lang('banned_hosts')" :multiple="false" classname="col-sm-6" :strlength="35"
                                    :required="true" name="BANNED_HOSTS" :elements="bannedHosts" :value="bannedHostsType"
                                    :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('autoban_failed_login_hosts')" classname="col-sm-6" :strlength="35"
                                    :required="true" name="FAILED_LOGINS_LIMIT" :elements="autobanFailedLoginOptions"
                                    :value="autobanFailedLogin" :onChange="onChange" optionLabel="title">
                    </dynamic-select>

                    <dynamic-select :label="lang('forget_failed_attempts')" classname="col-sm-6" :strlength="35"
                                    :required="true" name="FAILED_FORGET_LIMIT" :elements="ForgetFailedAttemptsOptions"
                                    :value="ForgetFailedAttempts" :onChange="onChange" optionLabel="title">
                    </dynamic-select>
                </div>

            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit()"><i
                        :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios'

    import { successHandler, errorHandler } from '../../helpers/responseHandler';

    import { getIdFromUrl } from '../../helpers/extraLogics';

    import { validateSecuritySettings } from "../../helpers/validator/validateSecuritySettings.js";

    import moment from 'moment'

    import TextField from "../../components/Reusable/FormField/TextField.vue";

    import NumberField from "../../components/Reusable/FormField/NumberField.vue";

    import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

    import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

    import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";

    export default {

        name: 'Security-Settings',

        data() {

            return {

                title: 'security_settings',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                whitelistedAccess: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                whitelistedAccessType: null,

                whiteListedIp: '',

                bannedHosts: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                bannedHostsType: null,

                autobanFailedLogin: { title: '3 Attempts', value: 3 },

                autobanFailedLoginOptions: [],

                autobanFailedLicensingOptions: [],

                ForgetFailedAttempts: null,

                ForgetFailedAttemptsOptions: [],

                settingId: 'new'

            }
        },

        async beforeMount() {

            const path = window.location.pathname

            await this.getSecurityDropdownOptions()

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

            async getSecurityDropdownOptions() {

                this.loading = true;

                return await axios.get("/api/admin/securityDropdown").then((res) => {
                    const options = res.data
                    if (options['failed logins limit']) {
                        this.autobanFailedLoginOptions = options['failed logins limit']
                    }
                    if (options['failed licensings limit']) {
                        this.autobanFailedLicensingOptions = options['failed licensings limit']
                    }
                    if (options['failed hosts forget']) {
                        this.ForgetFailedAttemptsOptions = options['failed hosts forget']
                    }

                    this.loading = false;

                }).catch((err) => {

                    this.loading = false;

                });
            },

            isValid() {

                const { errors, isValid } = validateSecuritySettings(this.$data);

                return isValid;
            },

            setFormData() {
                const securitySettings = this.$store.getters['getSecuritySettings']

                if (securitySettings) {

                    this.settingId = securitySettings.SETTING_ID ?? 'new'

                    this.whitelistedAccessType = this.whitelistedAccess.find((opt) => {
                        return opt.value === securitySettings.WHITELISTED_ACCESS
                    })

                    this.whiteListedIp = securitySettings.WHITELISTED_IP ?? null

                    this.bannedHostsType = this.bannedHosts.find((opt) => {
                        return opt.value === securitySettings.BANNED_HOSTS
                    })

                    this.ForgetFailedAttempts = this.ForgetFailedAttemptsOptions.find((opt) => {
                        return opt.value === securitySettings.FAILED_FORGET_LIMIT
                    })

                    this.autobanFailedLogin = this.autobanFailedLoginOptions.find((opt) => {
                        return opt.value === securitySettings.FAILED_LOGINS_LIMIT
                    })

                }
            },
            onChange(value, name) {
                if (name === 'WHITELISTED_ACCESS') {
                    this.whitelistedAccessType = value
                } else if (name === 'WHITELISTED_IP') {
                    this.whiteListedIp = value
                } else if (name === 'BANNED_HOSTS') {
                    this.bannedHostsType = value
                }  else if (name === 'FAILED_LOGINS_LIMIT') {
                    this.autobanFailedLogin = value
                }  else if (name === 'FAILED_FORGET_LIMIT') {
                    this.ForgetFailedAttempts = value
                }
            },

            async onSubmit() {

                if (this.isValid()) {

                    this.loading = true

                    const formData = {

                        WHITELISTED_ACCESS: this.whitelistedAccessType ? this.whitelistedAccessType.value : null,

                        BANNED_HOSTS: this.bannedHostsType ? this.bannedHostsType.value : null,

                        FAILED_LOGINS_LIMIT: this.autobanFailedLogin ? this.autobanFailedLogin.value : null,

                        FAILED_FORGET_LIMIT: this.ForgetFailedAttempts ? this.ForgetFailedAttempts.value : null,

                        WHITELISTED_IP: this.whiteListedIp ?? null,
                    }

                    await axios.post(`/api/admin/securitysettings/${this.settingId}`, formData).then(async (res) => {


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

                "radio-button": RadioButton,

            }
        }
</script>

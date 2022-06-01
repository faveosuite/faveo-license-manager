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
                        :required="false" name="whitelisted_access" :elements="whitelistedAccess"
                        :value="whitelistedAccessType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="lang('whitelisted_ip')" :value="whiteListedIp" :onChange="onChange"
                        name="whitelisted_ip" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('banned_hosts')" :multiple="false" classname="col-sm-6" :strlength="35"
                        :required="false" name="banned_hosts" :elements="bannedHosts" :value="bannedHostsType"
                        :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="lang('message_for_banned_hosts')" :value="messageBannedHosts"
                        :onChange="onChange" name="message_for_banned_hosts" type="text" classname="col-sm-6">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('autoban_failed_login_hosts')" classname="col-sm-6" :strlength="35"
                        :required="false" name="autoban_failed_login_hosts" :elements="autobanFailedLoginOptions"
                        :value="autobanFailedLogin" :onChange="onChange" optionLabel="title">
                    </dynamic-select>

                    <dynamic-select :label="lang('autoban_failed_licensing_hosts')" classname="col-sm-6" :strlength="35"
                        :required="false" name="autoban_failed_licensing_hosts"
                        :elements="autobanFailedLicensingOptions" :value="autobanFailedLicensing" :onChange="onChange"
                        optionLabel="title">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('forget_failed_attempts')" classname="col-sm-6" :strlength="35"
                        :required="false" name="forget_failed_attempts" :elements="ForgetFailedAttemptsOptions"
                        :value="ForgetFailedAttempts" :onChange="onChange" optionLabel="title">
                    </dynamic-select>

                    <text-field :label="lang('minimum_password_length')" :value="minPasswordLength" :onChange="onChange"
                        name="minimum_password_length" type="number" classname="col-sm-6">

                    </text-field>

                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-default" @click="onSubmit()"><i
                        :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios'

    import { successHandler, errorHandler } from 'helpers/responseHandler';

    import { getIdFromUrl } from 'helpers/extraLogics';

    import { mapGetters } from 'vuex';

    import moment from 'moment'

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

                messageBannedHosts: null,

                autobanFailedLogin: null,

                autobanFailedLoginOptions: [],

                autobanFailedLicensing: null,

                autobanFailedLicensingOptions: [],

                ForgetFailedAttempts: null,

                ForgetFailedAttemptsOptions: [],

                minPasswordLength: null,

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

                    this.messageBannedHosts = securitySettings.BANNED_HOST_MESSAGE ?? null

                    this.autobanFailedLicensing = this.autobanFailedLicensingOptions.find((opt) => {
                        return opt.value === securitySettings.FAILED_LICENSINGS_LIMIT
                    })

                    this.ForgetFailedAttempts = this.ForgetFailedAttemptsOptions.find((opt) => {
                        return opt.value === securitySettings.FAILED_HOSTS_FORGET
                    })

                    this.autobanFailedLogin = this.autobanFailedLoginOptions.find((opt) => {
                        return opt.value === securitySettings.FAILED_LOGINS_LIMIT
                    })

                    this.minPasswordLength = securitySettings.MIN_PASSWORD_LENGTH ?? null
                }
            },
            onChange(value, name) {
                if (name === 'whitelisted_access') {
                    this.whitelistedAccessType = value
                } else if (name === 'whitelisted_ip') {
                    this.whiteListedIp = value
                } else if (name === 'banned_hosts') {
                    this.bannedHostsType = value
                } else if (name === 'message_for_banned_hosts') {
                    this.messageBannedHosts = value
                } else if (name === 'autoban_failed_login_hosts') {
                    this.autobanFailedLogin = value
                } else if (name === 'autoban_failed_licensing_hosts') {
                    this.autobanFailedLicensing = value
                } else if (name === 'forget_failed_attempts') {
                    this.ForgetFailedAttempts = value
                } else if (name === 'minimum_password_length') {
                    this.minPasswordLength = value
                }
            },

            async onSubmit() {
                const formData = {
                    MIN_PASSWORD_LENGTH: this.minPasswordLength ?? null,

                    WHITELISTED_ACCESS: this.whitelistedAccessType ? this.whitelistedAccessType.value : null,

                    BANNED_HOSTS: this.bannedHostsType ? this.bannedHostsType.value : null,

                    BANNED_HOST_MESSAGE: this.messageBannedHosts ?? null,

                    FAILED_LOGINS_LIMIT: this.autobanFailedLogin ? this.autobanFailedLogin.value : null,

                    FAILED_LICENSINGS_LIMIT: this.autobanFailedLicensing ? this.autobanFailedLicensing.value : null,

                    FAILED_HOSTS_FORGET: this.ForgetFailedAttempts ? this.ForgetFailedAttempts.value : null,

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
        },

        components: {

            "text-field": require("components/Reusable/FormField/TextField").default,

            "number-field": require("components/Reusable/FormField/NumberField").default,

            "static-select": require("components/Reusable/FormField/StaticSelect").default,

            "dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,

            "radio-button": require("components/Reusable/FormField/RadioButton").default,

        }
    }
</script>
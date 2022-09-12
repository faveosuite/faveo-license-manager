<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Configure reminder email settings, enable and disable individual options.</p><br>

            <p><b>Attention</b>: reminder emails will only be sent to personal (email-based) license owners who have
                their email addresses set.</p>
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

                    <text-field :label="lang('from_name')" :value="fromName" :onChange="onChange" name="EMAIL_FROM_NAME"
                        type="text" classname="col-sm-6" :required="true">

                    </text-field>

                    <text-field :label="lang('from_address')" :value="fromAddress" :onChange="onChange"
                        name="EMAIL_FROM_ADDRESS" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('send_copy_sender')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="EMAIL_CC_SENDER" :elements="copySender"
                        :value="copySenderType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('expiring_license_reminder')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="EMAIL_EXPIRING_LICENSE_DAYS" :elements="licenseReminder"
                        :value="licenseReminderType" :onChange="onChange">
                    </dynamic-select>
                </div>

                <div class="row">

                    <dynamic-select :label="lang('expiring_updates_reminder')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="EMAIL_EXPIRING_UPDATES_DAYS" :elements="updatesReminder"
                        :value="updatesReminderType" :onChange="onChange">
                    </dynamic-select>

                    <dynamic-select :label="lang('expiring_support_reminder')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="true" name="EMAIL_EXPIRING_SUPPORT_DAYS" :elements="supportReminder"
                        :value="supportReminderType" :onChange="onChange">
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

    import { validateEmailSettings } from "../../helpers/validator/validateEmailSettings.js";

    import moment from 'moment'

    import TextField from "../../components/Reusable/FormField/TextField.vue";

    import NumberField from "../../components/Reusable/FormField/NumberField.vue";

    import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

    import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

    export default {

        name: 'Email-Settings',

        data() {

            return {

                title: 'email_settings',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                fromName: null,

                fromAddress: null,

                copySender: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                copySenderType: null,

                licenseReminder: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                licenseReminderType: null,

                updatesReminder: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                updatesReminderType: null,

                supportReminder: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],

                supportReminderType: null,

                settingId: 'new'
            }
        },

        async beforeMount() {

            const path = window.location.pathname

            await this.getEmailDropdownOptions()

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

            async getEmailDropdownOptions() {
                this.loading = true;

                return await axios.get("/api/admin/emailDropdown").then((res) => {
                    const options = res.data

                    if (options['email expiring license days']) {
                        this.licenseReminderType = options['email expiring license days']
                    }
                    if (options['email expiring updates days']) {
                        this.updatesReminderType = options['email expiring updates days']
                    }
                    if (options['email expiring support days']) {
                        this.supportReminderType = options['email expiring support days']
                    }

                    this.loading = false;

                }).catch((err) => {

                    this.loading = false;

                });
            },

            isValid() {

                const { errors, isValid } = validateEmailSettings(this.$data);

                return isValid;
            },

            setFormData() {

                const emailSettings = this.$store.getters['getEmailSettings']

                if (emailSettings) {

                    this.settingId = emailSettings.SETTING_ID ?? 'new'

                    this.fromName = emailSettings.EMAIL_FROM_NAME ?? null

                    this.fromAddress = emailSettings.EMAIL_FROM_ADDRESS ?? null

                    this.copySenderType = this.copySender.find((opt) => {
                        return opt.value === emailSettings.EMAIL_CC_SENDER
                    })

                    this.licenseReminderType = this.licenseReminder.find((opt) => {
                        return opt.value === emailSettings.EMAIL_EXPIRING_LICENSE_DAYS
                    })

                    this.updatesReminderType = this.updatesReminder.find((opt) => {
                        return opt.value === emailSettings.EMAIL_EXPIRING_UPDATES_DAYS
                    })

                    this.supportReminderType = this.supportReminder.find((opt) => {
                        return opt.value === emailSettings.EMAIL_EXPIRING_SUPPORT_DAYS
                    })
                }
            },

            onChange(value, name) {

                if (name === 'EMAIL_FROM_NAME') {
                    this.fromName = value
                } else if (name === 'EMAIL_FROM_ADDRESS') {
                    this.fromAddress = value
                } else if (name === 'EMAIL_CC_SENDER') {
                    this.copySenderType = value
                } else if (name === 'EMAIL_EXPIRING_LICENSE_DAYS') {
                    this.licenseReminderType = value
                } else if (name === 'EMAIL_EXPIRING_UPDATES_DAYS') {
                    this.updatesReminderType = value
                } else if (name === 'EMAIL_EXPIRING_SUPPORT_DAYS') {
                    this.supportReminderType = value
                }
            },

            async onSubmit() {

                if (this.isValid()) {

                    this.loading = true

                    const formData = {

                        EMAIL_FROM_NAME: this.fromName ?? null,

                        EMAIL_FROM_ADDRESS: this.fromAddress ?? null,

                        EMAIL_CC_SENDER: this.copySenderType ? this.copySenderType.value : null,

                        EMAIL_EXPIRING_LICENSE_DAYS: this.licenseReminderType ? this.licenseReminderType.value : null,

                        EMAIL_EXPIRING_UPDATES_DAYS: this.updatesReminderType ? this.updatesReminderType.value : null,

                        EMAIL_EXPIRING_SUPPORT_DAYS: this.supportReminderType ? this.supportReminderType.value : null,
                    }

                    await axios.post(`/api/admin/emailsettings/${this.settingId}`, formData).then(async (res) => {


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

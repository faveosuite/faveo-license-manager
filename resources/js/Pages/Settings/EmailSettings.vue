<template>

    <div class="col-sm-12">

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="settings"/>

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{ lang(title) }}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <dynamic-select :label="lang('driver')" :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="true" name="EMAIL_DRIVER" :elements="emailDrivers"
                                    :value="emailDriver" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="lang('email')" :value="emailFromAddress" :onChange="onChange"
                                name="EMAIL_FROM_ADDRESS" type="text" classname="col-sm-6" :required="true">
                    </text-field>

                </div>

                <div class="row">

                    <text-field :label="lang('from_name')" :value="emailFromName" :onChange="onChange"
                                name="EMAIL_FROM_NAME" type="text" classname="col-sm-6" :required="true">
                    </text-field>

                    <dynamic-select v-if="emailDriver && emailDriver.id === 'smtp'" :label="lang('encryption')"
                                    :multiple="false" classname="col-sm-6"
                                    :strlength="35" :required="true" name="EMAIL_ENCRYPTION"
                                    :elements="emailEncryptions"
                                    :value="emailEncryption" :onChange="onChange">
                    </dynamic-select>

                </div>

                <div class="row">
                    <number-field v-if="emailDriver && emailDriver.id === 'smtp'" :label="lang('port')"
                                  :value="emailPort" :onChange="onChange"
                                  name="EMAIL_PORT" type="number" classname="col-sm-6" :required="true">

                    </number-field>

                    <text-field v-if="emailDriver && emailDriver.id === 'smtp'" :label="lang('password')"
                                :value="emailPassword" :onChange="onChange"
                                name="EMAIL_PASSWORD" type="password" classname="col-sm-6" :required="true">

                    </text-field>
                </div>
                <div class="row">
                    <text-field v-if="emailDriver && emailDriver.id === 'smtp'" :label="lang('host')" :value="emailHost"
                                :onChange="onChange"
                                name="EMAIL_HOST" type="text" classname="col-sm-6" :required="true">

                    </text-field>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit()"><i
                    :class="iconClass"></i> {{ lang(btnName) }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios'

import {successHandler, errorHandler} from '../../helpers/responseHandler';

import moment from 'moment'

import {validateEmailSettings} from "../../helpers/validator/validateEmailSettings.js";

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

            settingId: 'new',

            emailDriver: null,

            emailPort: null,

            emailHost: null,

            emailEncryption: null,

            emailFromAddress: null,

            emailFromName: null,

            company: null,

            emailPassword: null,

            emailEncryptions:
                [
                    {value: 'None', name: 'None'},
                    {value: 'SSL', name: 'SSL'},
                    {value: 'TLS', name: 'TLS'},
                    {value: 'StartTLS', name: 'StartTLS'},
                ],

            emailDrivers: null,
        }
    },

    async beforeMount() {

        const path = window.location.pathname

        await this.getEmailDropdownOptions();

        this.loadData()

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

            return await axios.get("/api/admin/viewEmails").then((res) => {
                const options = res.data.data;
                if (options['prototypeDropdown']) {
                    this.emailDrivers = options['prototypeDropdown']
                }
                this.loading = false;

            }).catch((err) => {

                this.loading = false;

            });
        },

        isValid() {

            const {errors, isValid} = validateEmailSettings(this.$data);

            return isValid;
        },


        setFormData() {

            const emailSettings = this.$store.getters['getEmailSettings']

            if (emailSettings) {

                this.settingId = emailSettings.SETTING_ID ?? 'new'

                this.emailDriver = emailSettings.EMAIL_DRIVER ?
                    (emailSettings.EMAIL_DRIVER.toLowerCase() === 'mail' ?
                        {'name' : 'Php Mail', 'id' : 'mail' } :
                        emailSettings.EMAIL_DRIVER.toLowerCase() === 'smtp' || emailSettings.EMAIL_DRIVER.toUpperCase() === 'SMTP' ?
                            {'name' : 'SMTP', 'id' : 'smtp' } :
                            emailSettings.EMAIL_DRIVER)
                    : null;

                this.emailPort = emailSettings.EMAIL_PORT ?? null

                this.emailHost = emailSettings.EMAIL_HOST ?? null

                this.emailFromAddress = emailSettings.EMAIL_FROM_ADDRESS ?? null

                this.emailEncryption = emailSettings.EMAIL_ENCRYPTION ?? null

                this.emailFromName = emailSettings.EMAIL_FROM_NAME ?? null

                this.emailPassword = emailSettings.EMAIL_PASSWORD ?? null
            }
        },

        onChange(value, name) {
            const propertyMap = {
                'EMAIL_DRIVER': 'emailDriver',
                'EMAIL_PORT': 'emailPort',
                'EMAIL_HOST': 'emailHost',
                'EMAIL_ENCRYPTION': 'emailEncryption',
                'EMAIL_FROM_ADDRESS': 'emailFromAddress',
                'EMAIL_FROM_NAME': 'emailFromName',
                'EMAIL_PASSWORD': 'emailPassword',
            };
            const propertyName = propertyMap[name];

            if (name === 'EMAIL_DRIVER' && value !== 'smtp') {
                this.emailPassword = null;
                this.emailHost = null;
                this.emailEncryption = null;
            }

            if (propertyName !== undefined) {
                this[propertyName] = (propertyName === 'emailEncryption' && typeof value === 'object') ? value.value : value;
            }
        },


        async onSubmit() {

            if (this.isValid()) {
                this.loading = true;
                const formData = {
                    EMAIL_DRIVER: this.emailDriver ? this.emailDriver.id ?? null : null,
                    EMAIL_PORT: this.emailPort ?? null,
                    EMAIL_HOST: this.emailHost ?? null,
                    EMAIL_FROM_ADDRESS: this.emailFromAddress ?? null,
                    EMAIL_FROM_NAME: this.emailFromName ?? null,
                    EMAIL_PASSWORD: this.emailPassword ?? null,
                    EMAIL_ENCRYPTION: this.emailEncryption ?? null,
                };

                try {
                    const response = await axios.post(`/api/admin/emailSettings`, formData);
                    successHandler(response, 'settings');
                    await this.$store.dispatch('fetchSettings');
                } catch (error) {
                    errorHandler(error, 'settings');
                } finally {
                    this.loading = false;
                }
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

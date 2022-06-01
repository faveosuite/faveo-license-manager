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

                    <dynamic-select :label="lang('auto_php_licenser')" :multiple="false" classname="col-sm-6"
                        :strlength="35" :required="false" name="auto_php_licenser" :elements="autoPhpLicenser"
                        :value="autoPhpLicenserType" :onChange="onChange">
                    </dynamic-select>

                    <text-field :label="lang('evanto_api_token')" :value="evantoApiToken" :onChange="onChange"
                        name="evanto_api_token" type="text" classname="col-sm-6">

                    </text-field>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-default" @click="onSubmit"><i
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

        name: 'Advanced-Settings',

        data() {

            return {

                title: 'advanced_settings',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                autoPhpLicenser: [
                    { name: 'Enabled', value: 1 },
                    { name: 'Disabled', value: 0 }
                ],
                autoPhpLicenserType: null,

                evantoApiToken: null,

                settingId: 'new'
            }
        },

        beforeMount() {

            const path = window.location.pathname

            this.loadData();
        },

        computed: {

            ...mapGetters(['getApiKey'])
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
            setFormData() {
                const advancedSettings = this.$store.getters['getAdvancedSettings']

                if (advancedSettings) {

                    this.settingId = advancedSettings.SETTING_ID ?? 'new'

                    this.autoPhpLicenserType = this.autoPhpLicenser.find((opt) => {
                        return opt.value === advancedSettings.API_STATUS
                    })

                    this.evantoApiToken = advancedSettings.ENVATO_API_TOKEN ?? null
                }
            },

            onChange(value, name) {

                if (name === 'auto_php_licenser') {
                    this.autoPhpLicenserType = value
                } else if (name === 'evanto_api_token') {
                    this.evantoApiToken = value
                }
            },


            async onSubmit() {
                const formData = {
                    API_STATUS: this.autoPhpLicenserType ? this.autoPhpLicenserType.value : null,
                }

                if (this.evantoApiToken) {
                    formData.ENVATO_API_TOKEN = this.evantoApiToken
                }

                await axios.post(`/api/admin/advancedsettings/${this.settingId}`, formData).then(async (res) => {

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
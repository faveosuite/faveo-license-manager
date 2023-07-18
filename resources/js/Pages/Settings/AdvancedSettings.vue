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
                        :strlength="35" :required="true" name="API_STATUS" :elements="autoPhpLicenser"
                        :value="autoPhpLicenserType" :onChange="onChange">
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

    import { successHandler, errorHandler } from '../../helpers/responseHandler';

    import { getIdFromUrl } from '../../helpers/extraLogics';

    import { validateAdvancedSettings } from "../../helpers/validator/validateAdvancedSettings";

    import moment from 'moment'

    import TextField from "../../components/Reusable/FormField/TextField.vue";

    import NumberField from "../../components/Reusable/FormField/NumberField.vue";

    import StaticSelect from "../../components/Reusable/FormField/StaticSelect.vue";

    import DynamicSelect from "../../components/Reusable/FormField/DynamicSelect.vue";

    import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";

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

            isValid() {

                const { errors, isValid } = validateAdvancedSettings(this.$data);

                return isValid;
            },

            onChange(value, name) {

                if (name === 'API_STATUS') {
                    this.autoPhpLicenserType = value
                } else if (name === 'ENVATO_API_TOKEN') {
                    this.evantoApiToken = value
                }
            },


            async onSubmit() {
                if (this.isValid()) {

                    this.loading = true
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

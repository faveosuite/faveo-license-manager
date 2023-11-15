<template>

    <div class="col-sm-12">

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="client" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('first_name')" :value="client_fname" type="text" name="client_fname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>

                    <text-field :label="lang('last_name')" :value="client_lname" type="text" name="client_lname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="lang('email_address')" :value="client_email" type="text" name="client_email"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>

                   
                </div>

                <div class="row">
                    <radio-button :options="radioOptions" :label="lang('status')" name="client_status"
                                  :value="client_status" :onChange="onChange" classname="form-group col-sm-6">

                    </radio-button>
                    <radio-button :options="radioOption" :label="lang('role')" name="client_role"
                                  :value="client_role" :onChange="onChange" classname="form-group col-sm-6">

                    </radio-button>
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

import { validateClientSettings } from "../../helpers/validator/clientValidation";

import TextField from "../../components/Reusable/FormField/TextField.vue";

import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";
import { computed }  from 'vue';
import { useStore } from 'vuex';

export default {

    name: 'client-create-edit',
    setup() {

const store = useStore();

return {
    getApiKey: computed(() => store.getters.getApiKey)
};
},

    data() {

        return {

            title: 'create_new_contact',

            iconClass: 'fas fa-save',

            btnName: 'save',

            hasDataPopulated: false,

            loading: false,

            radioOptions: [{ name: 'active', value: 1 }, { name: 'inactive', value: 0 }],

            radioOption:[{ name: 'Client', value: 1}, {name:  'Admin',  value:0}],

            client_fname: '',

            client_lname: '',

            client_status: 1,

            client_role: 1,

            client_email: '',

            apiEndpoint: '',

            client_id: ''
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);
    },

    methods: {

        getValues(path) {

            const clientId = getIdFromUrl(path)

            if (path.indexOf('edit') >= 0) {

                this.title = 'edit_client'

                this.iconClass = 'fas fa-sync'

                this.btnName = 'update'

                this.hasDataPopulated = false

                this.getInitialValues(clientId);

                this.client_id = clientId;

                this.apiEndpoint = '/api/admin/clients/edit';

            } else {

                this.loading = false;

                this.hasDataPopulated = true;

                this.apiEndpoint = '/api/admin/clients/add';
            }
        },

        getInitialValues(id) {

            this.loading = true

            axios.get('/api/admin/client/' + id).then(res => {

                this.loading = false;

                this.hasDataPopulated = true

                this.updateStatesWithData(res.data.data.client);

            }).catch(error => {

                this.loading = false;
            });
        },

        updateStatesWithData(data) {

            const self = this;

            const stateData = this.$data;

            Object.keys(data).map(key => {

                if (stateData.hasOwnProperty(key)) {

                    self[key] = data[key];
                }
            });
        },

        isValid() {

            const { errors, isValid } = validateClientSettings(this.$data);

            return isValid;
        },

        onChange(value, name) {

            if(name == 'client_status') {

                this[name] = value;

            } else {

                this[name] = value ? value : '';
            }
            this.client_role = value ? 1 : 0;
        },

        onSubmit() {

            if (this.isValid()) {

                this.loading = true

                const data = {};

                if (this.client_id) {

                    data['client_id'] = this.client_id;
                }

                data['api_key_secret'] = this.getApiKey;

                data['client_fname'] = this.client_fname;

                data['client_lname'] = this.client_lname;

                data['client_email'] = this.client_email;

                data['client_status'] = this.client_status ? 1 : 0;

                data['client_role']  =this.client_role ? 1 : 0;

                axios.post(this.apiEndpoint, data).then(res => {

                    this.loading = false

                    successHandler(res, 'client')

                    if (!this.client_id) {

                        setTimeout(() => {

                            this.$router.push('/clients')

                        }, 2000)

                    } else {

                        this.getInitialValues(this.client_id)
                    }

                }).catch(err => {

                    this.loading = false

                    errorHandler(err, 'client')
                });
            }
        }
    },

    components: {

        "text-field": TextField,

        "radio-button": RadioButton
    }
}
</script>
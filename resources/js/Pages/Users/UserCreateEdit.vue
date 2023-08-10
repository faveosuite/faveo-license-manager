<template>

    <div class="col-sm-12">
        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="user" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('first_name')" :value="admin_fname" type="text" name="admin_fname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>

                    <text-field :label="lang('last_name')" :value="admin_lname" type="text" name="admin_lname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="lang('email_address')" :value="admin_email" type="text" name="admin_email"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>


                    <radio-button :options="radioOptions" :label="lang('admin_status')" name="admin_status"
                                  :value="admin_status" :onChange="onChange" classname="form-group col-sm-6">

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

import { validateUserSettings } from "../../helpers/validator/userSettings";

import TextField from "../../components/Reusable/FormField/TextField.vue";

import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";
import DateTimePicker from "../../components/Reusable/FormField/DateTimePicker.vue";
export default {

    name: 'user-create-edit',

    data() {

        return {

            isUpdating : false,

            title: 'create_new_user',

            iconClass: 'fas fa-save',

            btnName: 'save',

            hasDataPopulated: false,

            loading: false,

            radioOptions: [{ name: 'active', value: 1 }, { name: 'inactive', value: 0 }],

            admin_fname: '',

            admin_lname: '',

            admin_status: 1,

            admin_email: '',

            admin_date:'',

            apiEndpoint: '',

            admin_id: ''
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);
    },

    methods: {

        getValues(path) {

            const adminId = getIdFromUrl(path)

            if (path.indexOf('edit') >= 0) {

                this.title = 'edit_user'

                this.iconClass = 'fas fa-sync'

                this.btnName = 'update'

                this.hasDataPopulated = false

                this.getInitialValues(adminId);

                this.admin_id = adminId;

                this.apiEndpoint = '';

            } else {

                this.loading = false;

                this.hasDataPopulated = true;

                this.apiEndpoint = '/api/admin/addusers';
            }
        },

        getInitialValues(id) {
            this.loading=true

            axios.get('/api/admin/editusers/' + id).then(res => {
                this.loading =false;

                this.hasDataPopulated =true

                this.isUpdating = true;

                this.updateStatesWithData(res.data.data);

            }).catch(error => {

                this.loading = false;

                errorHandler(err, 'user')
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

            const { errors, isValid } = validateUserSettings(this.$data);

            return isValid;
        },

        onChange(value, name) {
            if (name === 'admin_status') { // Update the name to 'admin_status'
                this[name] = value;
            } else {
                this[name] = value ? value : '';
            }
        },

        onSubmit() {

                this.loading =true

                const data={};

                    data['admin_fname'] =this.admin_fname;

                    data['admin_lname'] =this.admin_lname;

                    data['admin_email'] =this.admin_email;

                    data['admin_status'] =this.admin_status;

                    if(this.isUpdating){
                        data['id'] = this.admin_id;
                    }

                    axios.post('/api/admin/updateuser/'+this.admin_id,data).then(res =>{

                        this.loading =false

                        successHandler(res, 'user')

                        if (!this.user_id) {

                            setTimeout(() => {

                                this.$router.push('/users')

                            }, 2000)

                        } else {

                            this.getInitialValues(this.admin_id)
                        }
                    }).catch(err => {

                        this.loading = false

                        errorHandler(err, 'user')
                    });


        }
    },

    components: {

        "text-field": TextField,

        "radio-button": RadioButton,

        DateTimePicker

    }
}
</script>

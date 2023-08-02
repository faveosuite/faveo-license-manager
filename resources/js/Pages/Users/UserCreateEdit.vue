<template>

    <div class="col-sm-12">

        <alert componentName="user" />

        <div class="card card-light">

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
                    
                    <date-picker :label="trans('license_expire_date')" :value="license_expire_date" type="date"
                                 name="license_expire_date" :onChange="onChange" :required="true" format="DD-MM-YYYY"
                                 classname="col-sm-6" :clearable="true" :disabled="false" :confirm="false">

                    </date-picker>

                    <radio-button :options="radioOptions" :label="lang('status')" name="admin_status"
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
export default {

    name: 'user-create-edit',

    data() {

        return {

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

            apiEndpoint: '',
        }
    },

    beforeMount() {

        const path = window.location.pathname

        this.getValues(path);
    },

    methods: {

        getValues(path) {

            const userId = getIdFromUrl(path)

            if (path.indexOf('edit') >= 0) {

                this.title = 'edit_user'

                this.iconClass = 'fas fa-sync'

                this.btnName = 'update'

                this.hasDataPopulated = false

                this.getInitialValues(userId);

                this.user_id = userId;

                this.apiEndpoint = '/api/admin/editusers';

            } else {

                this.loading = false;

                this.hasDataPopulated = true;

                this.apiEndpoint = '/api/admin/addusers';
            }
        },

        getInitialValues(id) {

            this.loading=true

            axios.get('/api/admin/getusers/' + id).then(res => {
                this.loading =false;

                this.hasDataPopulated =true

                this.updateStatesWithData(res.data.data.user);

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

            const { errors, isValid } = validateUserSettings(this.$data);

            return isValid;
        },

        onChange(value, name) {

            if(name == 'status') {

                this[name] = value;

            } else {

                this[name] = value ? value : '';
            }
        },

        onSubmit() {
            console.log('Form is submitted.');


           
                console.log('hello')
                this.loading =true

                const data={};

               
                    console.log('test')
                    data['admin_fname'] =this.admin_fname;

                    data['admin_lname'] =this.admin_lname;

                    data['admin_email'] =this.admin_email;

                    data['admin_status'] =this.admin_status;

                    axios.post(this.apiEndpoint,data).then(res =>{
                        console.log('test',this.res)
                        this.loading =false

                        successHandler(res, 'user')

                        if (!this.user_id) {

                            setTimeout(() => {

                                this.$router.push('/users')

                            }, 2000)

                        } else {

                            this.getInitialValues(this.user_id)
                        }
                    }).catch(err => {

                        this.loading = false

                        errorHandler(err, 'user')
                    });
          

        }
    },

    components: {

        "text-field": TextField,

        "radio-button": RadioButton

    }
}
</script>

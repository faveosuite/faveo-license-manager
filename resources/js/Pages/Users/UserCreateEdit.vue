<template>

    <div class="col-sm-12">

        <alert componentName="user" />

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('first_name')" :value="user_fname" type="text" name="user_fname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>

                    <text-field :label="lang('last_name')" :value="user_lname" type="text" name="user_lname"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>
                </div>

                <div class="row">

                    <text-field :label="lang('email_address')" :value="user_email" type="text" name="user_email"
                                :onChange="onChange" classname="col-sm-6" :required="true">

                    </text-field>

                    <radio-button :options="radioOptions" :label="lang('status')" name="user_status"
                                  :value="user_status" :onChange="onChange" classname="form-group col-sm-6">

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

            user_fname: '',

            user_lname: '',

            user_status: 1,

            user_email: '',

            apiEndpoint: '',

            user_id: ''
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

                this.apiEndpoint = '/editusers';

            } else {

                this.loading = false;

                this.hasDataPopulated = true;

                this.apiEndpoint = '/addusers';
            }
        },

        getInitialValues(id) {

            this.loading=true

            axios.get('addusers' +id).then(res =>{

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

        onChange(value, name) {

            if(name == 'status') {

                this[name] = value;

            } else {

                this[name] = value ? value : '';
            }
        },

        onSubmit() {
            console.log('hello')
            this.loading =true

            const data={};

            if (this.user_id){
                console.log(test)

                data['user_id']  =this.user_id;

                data['user_fname'] =this.user_fname;

                data['user_lname'] =this.user_lname;

                data['user_email'] =this.user_email;

                data['user_status'] =this.user_status;

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

        }
    },

    components: {

        "text-field": TextField,

        "radio-button": RadioButton
    }
}
</script>

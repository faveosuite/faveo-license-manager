<template>

    <div class="col-sm-12">

<!--        <alert componentName="edit_agent_profile"/>-->

<!--        <div class="row" v-if="!hasDataPopulated || loading">-->

<!--            <custom-loader :duration="4000"></custom-loader>-->

<!--        </div>-->

        <div class="row">

            <div class="col-md-6">

                <div class="card card-light ">

                    <div class="card-header">

                        <h3 class="card-title">{{lang('profile')}}</h3>

                    </div>

                    <div class="card-body">

                        <div class="text-center">

                            <image-upload :label="lang('profile_pic')" :value="profile_pic" name="profile_pic" :onChange="onChange"
                                          :labelStyle="labelStyle" :labelCss="labelCss" buttonName="change">

                            </image-upload>
                        </div>

                        <text-field :label="lang('first_name')" :value="first_name" type="text" name="first_name"
                                    :onChange="onChange" :placehold="lang('enter_a_value')" :required="true" :autofocus="true">

                        </text-field>

                        <text-field :label="lang('last_name')" :value="last_name" type="text" name="last_name"
                                    :onChange="onChange" :placehold="lang('enter_a_value')">

                        </text-field>

<!--                        <dynamic-select v-if="types.length > 0" :label="lang('type')" :multiple="true" name="types" :required="false"-->
<!--                                        apiEndpoint="/api/dependency/types" :value="types" :onChange="onChange" :clearable="types ? true : false"-->
<!--                                        :disabled="true">-->

<!--                        </dynamic-select>-->

                        <text-field :label="lang('email')" :value="email" type="text" name="email" :onChange="onChange" :placehold="lang('enter_email_addresses')" :disabled="true">

                        </text-field>

<!--                        <dynamic-select v-if="location" :label="lang('location')" :multiple="false" name="location"-->
<!--                                        apiEndpoint="/api/dependency/locations" :value="location" :onChange="onChange" :clearable="location ? true : false"-->
<!--                                        :disabled="true">-->

<!--                        </dynamic-select>-->

                        <div class="row">

                            <phoneWithCountryCode id="phone_number" classname="col-sm-9" name="phone_number" :onChange="onChange" :value="phone_number"
                                                  :countryCode="phone_country_code" :countryIso="phone_iso" @countCode="getPCountCode" @countIso="getPCountIso"
                                                  labelName="phone_number" :apiUrl="'/api/dependency/country-codes?time='+Date.now()">
                            </phoneWithCountryCode>

                            <number-field :label="lang('ext')" :value="ext" name="ext" classname="col-sm-3"
                                          :onChange="onChange" type="number">

                            </number-field>

                        </div>

                        <phoneWithCountryCode id="mobile" name="mobile" :onChange="onChange" :value="mobile" :countryCode="country_code"
                                              @countCode="getCountCode" :countryIso="iso" @countIso="getCountIso">

                        </phoneWithCountryCode>


                        <tiny-editor :value="agent_sign" type="text" :onChange="onChange" name="agent_sign" :label="lang('agent_sign')"
                                     classname="" :lang="'en'">

                        </tiny-editor>

                        <radio-button :options="radioOptions" :label="lang('for_auto_assign_accept_tickets')" name="not_accept_ticket"
                                      :value="not_accept_ticket" :onChange="onChange" classname="form-group">

                        </radio-button>

                    </div>

                    <div class="card-footer">

                        <button class="btn btn-primary" @click="onSubmit()" :disabled="isDisabled">

                            <i class="fas fa-sync"></i> {{lang('update')}}
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6" v-if="hasDataPopulated">

                <div class="card card-light ">

                    <div class="card-header">

                        <h3 class="card-title">{{lang('change_password')}}</h3>
                    </div>

                    <div class="card-body">

                        <text-field :label="lang('old_password')" :value="old_password" type="password" name="old_password"
                                    :onChange="onChange" :placehold="lang('enter_a_value')" :required="true">

                        </text-field>
                        <text-field :label="lang('new_password')" :value="new_password" type="password" name="new_password"
                                    :onChange="onChange" :placehold="lang('enter_a_value')" :required="true" id="new_password">

                        </text-field>
                        <text-field :label="lang('confirm_password')" :value="confirm_password" type="password"
                                    name="confirm_password" :onChange="onChange" :placehold="lang('enter_a_value')" :required="true" id="confirm_password">

                        </text-field>
                    </div>

                    <div class="card-footer">

                        <button class="btn btn-primary" @click="onUpdatePassword()" :disabled="passDisabled">

                            <i class="fas fa-sync"></i> {{lang('update')}}
                        </button>
                    </div>
                </div>

                <div class="card card-light ">

                    <div class="card-header">

                        <h3 class="card-title">{{lang('2fa_setup')}}</h3>
                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-9">

								<span>

									<img class="img-responsive img-circle img-sm" :src="basePath()+'/themes/default/common/images/authenticator.png'" alt="A"
                                         id="auth_img">&nbsp;{{two_factor ? '2-Step Verification is ON since '+formattedTime(profileData.google2fa_activation_date)  : lang('authenticator_app')}}
								</span>
                            </div>

                            <div class="col-md-3">

                                <button v-if="!two_factor" type="button" class="btn btn-primary float-right" @click="showModal = true">

                                    <i class="fas fa-toggle-on"></i> {{lang('turn_on')}}

                                </button>

                                <button v-if="two_factor" type="button" class="btn btn-danger float-right" @click="removeModal = true">

                                    <i class="fas fa-power-off"></i> {{lang('turn_off')}}

                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="profile-view-boxes">{{profileBoxVisible()}}</div>

<!--                <login-logs :userId="profileData.id" :showBtn="true"></login-logs>-->

<!--                <transition name="modal">-->

<!--                    <barcode-modal v-if="showModal" :onClose="onClose" :showModal="showModal">-->

<!--                    </barcode-modal>-->
<!--                </transition>-->

<!--                <transition name="modal">-->

<!--                    <remove-modal v-if="removeModal" :onClose="onClose" :showModal="removeModal" alertName="edit_agent_profile">-->

<!--                    </remove-modal>-->
<!--                </transition>-->
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';

import { errorHandler, successHandler} from "../../helpers/responseHandler";

// import { validateAgentProfileSettings } from "../../../../helpers/validator/agentProfileSettings";

import { validatePasswordSettings } from "../../helpers/validator/passwordSettings";

import {useStore} from "vuex";
import {computed} from "vue";

import TextField from "../../components/Reusable/FormField/TextField.vue";
import RadioButton from "../../components/Reusable/FormField/RadioButton.vue";
import NumberField from "../../components/Reusable/FormField/NumberField.vue";
import PhoneWithCountryCode from "../../components/Reusable/FormField/PhoneWithCountryCode.vue";
// import DynamicSelect from "../../../MiniComponent/FormField/DynamicSelect.vue";
import ImageUpload from "../../components/Reusable/ImageUpload.vue";
// import BarcodeModal from "./BarcodeModal.vue";
// import RemoveVerification from "./RemoveVerification.vue";
// import LoginLogs from "../../Profile/LoginLogs.vue";

export default {

    name : 'agent-profie-edit',

    description  : 'Agent Profile Edit Page',

    setup() {

        const store = useStore();

        return {

            formattedTime : computed(()=>store.getters.formattedTime),

            formattedDate : computed(()=>store.getters.formattedDate)
        }
    },

    data(){

        return {

            loading : false,

            hasDataPopulated : false,

            isDisabled : false,

            passDisabled : false,

            first_name : '',

            last_name : '',

            email : '',

            country_code: 91,

            mobile: '',

            phone_number : '',

            phone_country_code : 91,

            ext : '',

            profile_pic : '',

            not_accept_ticket : 0,

            types : [],

            agent_sign : '',

            location : '',

            radioOptions:[{name:'yes',value:0},{name:'no',value:1}],

            labelStyle : { display : 'none' },

            labelCss : { visibility : 'hidden', margin : 'auto'},

            old_password : '',

            new_password : '',

            confirm_password : '',

            profileData : '',

            two_factor : false,

            showModal : false,

            removeModal : false,

            iso : '',

            phone_iso : '',

            selectedImage : ''
        }
    },

    created() {

        window.emitter.on('updateEditData',this.getData);
    },

    beforeMount(){

        this.getData()
    },

    methods : {

        getData(from){

            this.loading = true;

            axios.get('/api/profile/info').then(res=>{

                this.profileData = res.data.data;

                this.updateStatesWithData(res.data.data);

                if(from == 'update') {

                    window.emitter.emit('updateAgentProfile',res.data.data);
                }

                this.loading = false;

                this.hasDataPopulated = true;

            }).catch(error=>{

                this.loading = false;

                this.hasDataPopulated = true;

            });
        },

        updateStatesWithData(data){

            const self = this;

            const stateData = this.$data;

            Object.keys(data).map(key => {

                if (stateData.hasOwnProperty(key)) {

                    self[key] = data[key];

                }

            });

            this.two_factor = data.is_2fa_enabled;

            this.mobile = this.mobile === 'Not available' ? '' : this.mobile;

            this.country_code = this.country_code === '' ? 91 : this.country_code;

            this.phone_number = this.phone_number === 'Not available' ? '' : this.phone_number;

            this.phone_country_code = this.phone_country_code === '' ? 91 : this.phone_country_code;

        },

        onChange(value, name) {

            name === 'profile_pic' ? (this.profile_pic = value.image , this.selectedImage = value) : (this[name] = value);
        },

        getCountCode(value){

            this.country_code = value;
        },

        getCountIso(value){

            this.iso = value;
        },

        getPCountCode(value){

            this.phone_country_code = value;
        },

        getPCountIso(value){

            this.phone_iso = value;
        },

        isValid(){

            // const {errors, isValid} = validateAgentProfileSettings(this.$data);

           let isValid = false;

            if(!isValid){

                return false
            }
            return true
        },

        isPasswordValid(){

            const {errors, isValid} = validatePasswordSettings(this.$data);

            if(!isValid){

                return false
            }
            return true
        },

        onSubmit(){

            if(this.isValid()){

                this.isDisabled=true;

                this.loading=true;

                var fd = new FormData();

                fd.append('first_name', this.first_name);

                fd.append('last_name', this.last_name);

                fd.append('country_code', this.country_code ? this.country_code : null);

                fd.append('iso', this.iso ? this.iso : null);

                fd.append('mobile', this.mobile);

                fd.append('ext', this.ext ? this.ext : '');

                fd.append('phone_country_code', this.phone_country_code ? this.phone_country_code : null);

                fd.append('phone_iso', this.phone_iso ? this.phone_iso : null);

                fd.append('phone_number', this.phone_number ? this.phone_number : '');

                if(this.selectedImage){

                    fd.append('profile_pic', this.selectedImage.file,this.selectedImage.name);
                }else{

                    fd.append('profile_pic', null);
                }
                fd.append('location', this.location ? this.location.id : '');

                fd.append('agent_sign', this.agent_sign ? this.agent_sign : '');

                fd.append('not_accept_ticket', this.not_accept_ticket);

                if(this.types !== ''){

                    for(var i in this.types){

                        fd.append('type['+i+']', this.types[i].id);
                    }
                }

                fd.append('_method', "PATCH");

                axios.post('/agent-profile', fd).then(res=> {

                    successHandler(res,'edit_agent_profile');

                    this.isDisabled=false;

                    this.loading=false;

                    this.getData('update');

                }).catch(error=>{

                    this.loading=false;

                    this.isDisabled=false;

                    errorHandler(error,'edit_agent_profile')
                })
            }
        },

        onUpdatePassword(){

            var regex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{6,20}$/;

            var message = regex.exec(this.new_password);

            if(this.isPasswordValid()){

                if(this.old_password !== this.new_password){

                    if(message){

                        if(this.new_password === this.confirm_password){

                            this.passDisabled=true;

                            this.loading=true;

                            var fd = new FormData();;

                            fd.append('old_password',this.old_password);

                            fd.append('new_password',this.new_password);

                            fd.append('confirm_password',this.confirm_password);

                            fd.append('_method', "PATCH");

                            axios.post('/agent-profile-password/'+this.profileData.id,fd).then(res=> {

                                this.old_password = '';

                                this.new_password = '';

                                this.confirm_password = '';

                                this.loading=false;

                                this.passDisabled=false;

                                successHandler(res,'edit_agent_profile');

                            }).catch(error=>{

                                this.loading=false;

                                this.passDisabled=false;

                                errorHandler(error,'edit_agent_profile')
                            })
                        }else {

                            this.$store.dispatch('setValidationError', {'confirm_password' : 'Password does not match'})
                        }
                    } else {

                        this.$store.dispatch('setValidationError', {'new_password' : 'Password must have 8 characters and contain at least one Uppercase, one lowercase, one number and one special character'})
                    }
                }else {

                    this.$store.dispatch('setValidationError', {'new_password' : 'new password is same as old. Please choose a different password'})
                }
            }
        },

        profileBoxVisible() {
            window.emitter.emit('profile-box-mounted',{'alert' : 'edit_agent_profile'});
        },

        onClose(){

            this.showModal = false;

            this.removeModal = false;

            this.$store.dispatch('unsetValidationError');
        },
    },

    components : {

        "text-field": TextField,

        "radio-button": RadioButton,

        "number-field": NumberField,

        'phoneWithCountryCode': PhoneWithCountryCode,

        // 'dynamic-select' : DynamicSelect,

        'image-upload': ImageUpload,

        // 'barcode-modal' : BarcodeModal,

        // 'remove-modal' : RemoveVerification,

        // 'login-logs' : LoginLogs
    }
};
</script>

<style>
#setup_fa{
    background: #eee !important;
    background-color: #eee !important;
}
#fa_switch{
    margin-top: 4px;
}
#auth_img{
    width: 25px!important;
    height: 25px!important;
}
</style>

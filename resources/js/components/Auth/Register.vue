<template>

  <div class="login-page">

    <div class="login-logo">
    
      {{trans('auto')}}&nbsp;<b>Faveo</b>&nbsp;{{trans('licenser')}}
    </div>

    <div class="login-box">
  
      <div class="card">

        <div class="card-body login-card-body">
          
          <p class="login-box-msg">{{trans('register_new_membership')}}</p>

          <alert componentName="register"></alert>

          <text-field :labelStyle="labelStyle" :label="trans('firstname')" :value="first_name" 
            type="text" name="first_name" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="First Name" classname="" :required="true">
              
          </text-field>

          <text-field :labelStyle="labelStyle" :label="trans('lastname')" :value="last_name" 
            type="text" name="last_name" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Last Name" classname="" :required="true">
              
          </text-field>

          <text-field :labelStyle="labelStyle" :label="trans('email')" :value="email" 
            type="email" name="email" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Email" classname="" :required="true">
              
          </text-field>

          <text-field :labelStyle="labelStyle" :label="trans('password')" :value="password" 
            type="password" name="password" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Password" classname="" :required="true">
                  
          </text-field>

          <text-field :labelStyle="labelStyle" :label="trans('password')" :value="confirm" 
            type="password" name="confirm" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Confirm Password" classname="" :required="true">
                  
          </text-field>

          <div class="social-auth-links text-center mb-1">
        
            <a href="javascript:;" class="btn btn-block btn-primary" @click="onSubmit()">

              <i class="fas fa-user-plus"></i>&nbsp;&nbsp;{{trans('register')}}
            </a>
          </div>

          <p class="mb-0">
            
            <router-link to="/login" class="btn btn-block btn-primary">

              <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;{{trans('login')}}
            </router-link>
          </p>
        </div>
      </div>
    </div>

    <div v-if="loading">
        
      <custom-loader></custom-loader>
    </div>
  </div>
</template>
<script>

import { mapGetters } from 'vuex';

import {errorHandler, successHandler} from 'helpers/responseHandler'

import { validateRegisterSettings } from "helpers/validator/registerRules";

import axios from 'axios'

export default {

  name : 'Register',
  
  data () {

    return {

      first_name : '',
      
      last_name : '',

      email : '',

      password : '',

      confirm : '',

      labelStyle:{ display:'none' },

      loading : false,
    }
  },

  beforeMount() {

    if(this.getUserToken){
      
      this.$router.push({name:'Dashboard'})
    }
  },

  computed : {

    ...mapGetters(['getUserToken'])
  },

  methods : {
    
    onChange(value, name) {
      
      this[name] = value;
    },

    isValid() {
      
      const { errors, isValid } = validateRegisterSettings(this.$data);
        
      return isValid;
    },

    triggerEvent(event) {
      
      var key = event.which || event.keyCode;
      
      if (key === 13) { // 13 is enter

         this.onSubmit();
      }
    },

    onSubmit() {

      if(this.isValid()) {

        if(this.password === this.confirm){

          this.loading = true;

          let data = {}
          
          data['admin_fname'] = this.first_name;
          
          data['admin_lname'] = this.last_name;

          data['admin_email'] = this.email;

          data['admin_password'] = this.password;

          data["admin_password_confirmation"] = this.confirm;

          axios.post("/api/register",data).then((res) => {

            this.loading = false;

            successHandler(res,'register');

            setTimeout(()=>{

              this.$router.push('/login');

            },2000);

          }).catch((err) => {

            this.loading = false;

            errorHandler(err,'register');
          });

        } else {

          this.$store.dispatch('setValidationError', {'confirm' : 'Password does not match'})
        }
      }
    }
  },

  components : {

    "text-field": require("components/Reusable/FormField/TextField").default,
  }
};
</script>
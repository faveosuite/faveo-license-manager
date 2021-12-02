<template>

  <div class="login-page">

    <div class="login-logo">
    
      {{trans('auto')}}&nbsp;<b>Faveo</b>&nbsp;{{trans('licenser')}}
    </div>

    <div class="login-box">
  
      <div class="card">

        <div class="card-body login-card-body">
          
          <p class="login-box-msg">{{trans('login_to_start_your_session')}}</p>

          <alert componentName="login"></alert>

          <text-field :labelStyle="labelStyle" :label="trans('username')" :value="user_name" 
            type="text" name="user_name" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Email/Username" classname="" :required="true">
              
          </text-field>

          <text-field :labelStyle="labelStyle" :label="trans('password')" :value="password" 
            type="password" name="password" :keyupListener="triggerEvent" :onChange="onChange"
            placehold="Password" classname="" :required="true">
                  
          </text-field>

          <div class="social-auth-links text-center mb-1">
        
            <a href="javascript:;" class="btn btn-block btn-primary" @click="onSubmit()">

              <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;{{trans('login')}}
            </a>
          </div>
          
          <p class="mb-1">
            
            <router-link to="/forgot-password">{{trans('iforgot')}}</router-link>
          </p>
          
          <p class="mb-0">
            
            <router-link to="/register" class="btn btn-block btn-primary">

              <i class="fas fa-user-plus"></i>&nbsp;&nbsp;{{trans('register')}}
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

import { validateLoginSettings } from "helpers/validator/loginRules";

import axios from 'axios'

export default {

  name : 'Login',
  
  data () {

    return {

      user_name : '',

      password : '',

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
      
      const { errors, isValid } = validateLoginSettings(this.$data);
        
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

        this.loading = true;

        let data = {}
          
        data['admin_email'] = this.user_name
        
        data['admin_password'] = this.password
        
        axios.post("/api/login",data).then((res) => {
          
          this.loading = false;

          this.$store.dispatch('setLoggedInUserToken',res.data.data.token);
          
          this.$store.dispatch('setUserInfo',res.data.data.user);
          
          this.$router.push('/dashboard')

        }).catch((err) => {

          this.loading = false;

          errorHandler(err,'login');
        });
      }
    }
  },

  components : {

    "text-field": require("components/Reusable/FormField/TextField").default,
  }
};
</script>
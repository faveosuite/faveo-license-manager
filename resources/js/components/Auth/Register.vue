<template>

  <div class="login-page">

    <div class="login-logo">

      {{lang('auto')}}&nbsp;<b>Faveo</b>&nbsp;{{lang('licenser')}}
    </div>

    <div class="login-box">

      <div class="card">

        <div class="card-body login-card-body">

          <p class="login-box-msg">{{lang('register_new_membership')}}</p>

          <alert componentName="register"></alert>

          <div v-if="loading" class="mt-4 mb-4">

            <loader></loader>
          </div>

          <template v-if="!loading">

            <text-field :labelStyle="labelStyle" :label="lang('firstname')" :value="first_name" type="text"
              name="first_name" :keyupListener="triggerEvent" :onChange="onChange" placehold="First Name" classname=""
              :required="true">

            </text-field>

            <text-field :labelStyle="labelStyle" :label="lang('lastname')" :value="last_name" type="text"
              name="last_name" :keyupListener="triggerEvent" :onChange="onChange" placehold="Last Name" classname=""
              :required="true">

            </text-field>

            <text-field :labelStyle="labelStyle" :label="lang('email')" :value="email" type="email" name="email"
              :keyupListener="triggerEvent" :onChange="onChange" placehold="Email" classname="" :required="true">

            </text-field>

            <text-field :labelStyle="labelStyle" :label="lang('password')" :value="password" type="password"
              name="password" :keyupListener="triggerEvent" :onChange="onChange" placehold="Password" classname=""
              :required="true">

            </text-field>

            <text-field :labelStyle="labelStyle" :label="lang('password')" :value="confirm" type="password"
              name="confirm" :keyupListener="triggerEvent" :onChange="onChange" placehold="Confirm Password"
              classname="" :required="true">

            </text-field>

            <div class="social-auth-links text-center mb-1">

              <a href="javascript:;" class="btn btn-block btn-primary" @click="onSubmit()">

                <i class="fas fa-user-plus"></i>&nbsp;&nbsp;{{lang('register')}}
              </a>
            </div>

            <p class="mb-0">

              <router-link to="/login" class="btn btn-block btn-primary">

                <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;{{lang('login')}}
              </router-link>
            </p>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

  import { mapGetters } from 'vuex';

  import { errorHandler, successHandler } from 'helpers/responseHandler'

  import { validateRegisterSettings } from "helpers/validator/registerRules";

  import axios from 'axios'

  export default {

    name: 'Register',

    data() {

      return {

        first_name: '',

        last_name: '',

        email: '',

        password: '',

        confirm: '',

        labelStyle: { display: 'none' },

        loading: false,
      }
    },

    beforeMount() {

      if (this.getUserToken) {

        this.$router.push({ name: 'Dashboard' }).catch(err => { });
      }
    },

    computed: {

      ...mapGetters(['getUserToken'])
    },

    methods: {

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

        if (this.isValid()) {

          if (this.password === this.confirm) {

            this.$store.dispatch('unsetAlert');

            this.$store.dispatch('unsetValidationError');

            this.loading = true;

            let data = {}

            data['admin_fname'] = this.first_name;

            data['admin_lname'] = this.last_name;

            data['admin_email'] = this.email;

            data['admin_password'] = this.password;

            data["admin_password_confirmation"] = this.confirm;

            axios.post("/api/register", data).then((res) => {

              this.loading = false;

              successHandler(res, 'register');

              setTimeout(() => {

                this.$router.push('/login').catch(err => { });

              }, 2000);

            }).catch((err) => {

              this.loading = false;

              errorHandler(err, 'register');
            });

          } else {

            this.$store.dispatch('setValidationError', { 'confirm': 'Password does not match' })
          }
        }
      }
    },

    components: {

      "text-field": require("components/Reusable/FormField/TextField").default,
    }
  };
</script>
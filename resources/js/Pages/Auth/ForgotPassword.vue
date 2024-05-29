<template>

  <div class="login-page">

    <div class="login-logo">

      {{lang('agora')}}&nbsp;<b>License</b>&nbsp;{{lang('manager')}}
    </div>

    <div class="login-box">

      <div class="card">

        <div class="card-body login-card-body">

          <p class="login-box-msg">{{lang('forgot_password')}}</p>

          <alert componentName="forgot"></alert>

          <div v-if="loading" class="mt-4 mb-4">

            <loader></loader>
          </div>

          <template v-if="!loading">

            <text-field :labelStyle="labelStyle" :label="lang('email')" :value="email" type="email" name="email"
              :keyupListener="triggerEvent" :onChange="onChange" placehold="Email" classname="" :required="true">

            </text-field>

            <p class="mb-1">

                <div class="row">

                  <div class="col-sm-6">

                    <router-link to="/login">{{lang('know_password')}}</router-link>
                  </div>

                  <div class="col-sm-6">

                    <button type="button" class="btn btn-primary float-right" @click="onSubmit()">

                      <i class="fas fa-paper-plane"></i>&nbsp;&nbsp;{{lang('send')}}</button>
                  </div>
                </div>
            </p>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

  import { errorHandler, successHandler } from '../../helpers/responseHandler'

  import { validateForgotSettings } from "../../helpers/validator/forgotRules";

  import axios from 'axios'

  import TextField from "../../components/Reusable/FormField/TextField.vue";

  import {useStore} from "vuex";

  import {env} from "../../../../env";

  import {computed, onMounted, ref} from "vue";

  export default {

    name: 'forgot-password',

      setup() {

          const store = useStore();
          const getUserToken = computed(() => store.getters.getUserToken);
          const recaptchaToken = ref(null);

          const loadRecaptchaScript = () => {
              return new Promise((resolve, reject) => {
                  const script = document.createElement('script');
                  script.src = 'https://www.google.com/recaptcha/api.js?render=' + env.VITE_RECAPTCHA_SITE_KEY;
                  script.onload = resolve;
                  script.onerror = reject;
                  document.head.appendChild(script);
              });
          };

          onMounted(async () => {
              try {
                  await loadRecaptchaScript();
                  grecaptcha.ready(() => {
                      grecaptcha.execute(env.VITE_RECAPTCHA_SITE_KEY, { action: 'submit' }).then((token) => {
                          recaptchaToken.value = token;
                      });
                  });
              } catch (error) {

              }
          });

          return {
              getUserToken,
              recaptchaToken,
          };
      },

    data() {

      return {

        email: '',

        labelStyle: { display: 'none' },

        loading: false,
      }
    },

    beforeMount() {

      if (this.getUserToken) {

        this.$router.push({ name: 'Dashboard' });
      }
    },

    methods: {

      onChange(value, name) {

        this[name] = value;
      },

      isValid() {

        const { errors, isValid } = validateForgotSettings(this.$data);

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

          this.$store.dispatch('unsetAlert');

          this.$store.dispatch('unsetValidationError');

          this.loading = true;

          let data = {}

          data['admin_email'] = this.email;
            if(env.VITE_RECAPTCHA_SITE_KEY){
                data['g-recaptcha-response'] = this.recaptchaToken
            }

          axios.post("/api/forgot", data).then((res) => {

            this.loading = false;

            successHandler(res, 'forgot');

            setTimeout(() => {

              this.$router.push('/login')

            }, 4000);

          }).catch((err) => {

            this.loading = false;

            errorHandler(err, 'forgot');
          });
        }
      }
    },

    components: {

      "text-field": TextField,
    }
  };
</script>

<template>

    <div class="login-page">

        <div class="login-logo">

            {{lang('Agora')}}&nbsp;<b>License</b>&nbsp;{{lang('Manager')}}</div>

        <div class="login-box">

            <div class="card">

                <div class="card-body login-card-body">

                    <p class="login-box-msg">{{lang('login_to_start_your_session')}}</p>

                    <alert componentName="login"></alert>

                    <div v-if="loading" class="mt-4 mb-4">

                        <loader></loader>
                    </div>

                    <template v-if="!loading">

                        <text-field :labelStyle="labelStyle" :label="lang('username')" :value="user_name" type="text"
                                    name="user_name" :keyupListener="triggerEvent" :onChange="onChange" placehold="Email/Username"
                                    classname="" :required="true">

                        </text-field>

                        <text-field :labelStyle="labelStyle" :label="lang('password')" :value="password" type="password"
                                    name="password" :keyupListener="triggerEvent" :onChange="onChange" placehold="Password" classname=""
                                    :required="true">

                        </text-field>

                        <div class="social-auth-links text-center mb-1">

                            <a href="javascript:;" class="btn btn-block btn-primary" @click="onSubmit()">

                                <i class="fas fa-sign-in-alt"></i>&nbsp;&nbsp;{{lang('login')}}
                            </a>
                        </div>

                        <p class="mb-1">

                            <router-link to="/forgot-password">{{lang('iforgot')}}</router-link>
                        </p>

                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import { computed }  from 'vue';
import { useStore } from 'vuex';

import { errorHandler, successHandler } from '../../helpers/responseHandler'

import { validateLoginSettings } from "../../helpers/validator/loginRules.js";

import axios from 'axios'

import TextField from "../../components/Reusable/FormField/TextField.vue";

export default {

    name: 'Login',

    setup() {

        const store = useStore();

        return {
            // getter
            getUserToken: computed(() => store.getters.getUserToken)
        };
    },

    data() {

        return {

            user_name: '',

            password: '',

            labelStyle: { display: 'none' },

            loading: false,
        }
    },

    beforeMount() {

        if (this.getUserToken) {

            this.$router.push({ name: 'Dashboard' }).catch(err => { })
        }
    },


    methods: {

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

            if (this.isValid()) {

                this.$store.dispatch('unsetAlert');

                this.$store.dispatch('unsetValidationError');

                this.loading = true;

                let data = {}

                data['admin_email'] = this.user_name

                data['admin_password'] = this.password

                axios.post("/api/login", data).then((res) => {

                    this.loading = false;

                    this.$store.dispatch('setLoggedInUserToken', res.data.data.token);

                    this.$store.dispatch('setUserInfo', res.data.data.user);

                    this.$router.push('/dashboard').catch(err => { })

                }).catch((err) => {

                    this.loading = false;

                    errorHandler(err, 'login');
                });
            }
        }
    },


    components: {

        "text-field": TextField,
    }
};
</script>

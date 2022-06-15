<template>

    <div class="login-page">

        <div class="login-logo">

            {{lang('auto')}}&nbsp;<b>Faveo</b>&nbsp;{{lang('licenser')}}
        </div>

        <div class="login-box">

            <div class="card">

                <div class="card-body login-card-body">

                    <p class="login-box-msg">{{lang('reset_password')}}</p>

                    <alert componentName="reset"></alert>

                    <div v-if="loading" class="mt-4 mb-4">

                        <loader></loader>
                    </div>

                    <template v-if="!loading">

                        <text-field :labelStyle="labelStyle" :label="lang('email')" :value="email" type="text"
                            name="email" :keyupListener="triggerEvent" :onChange="onChange" placehold="Email"
                            :required="true">

                        </text-field>

                        <text-field :labelStyle="labelStyle" :label="lang('password')" :value="password" type="password"
                            name="password" :keyupListener="triggerEvent" :onChange="onChange" placehold="New Password"
                             :required="true">

                        </text-field>

                        <text-field :labelStyle="labelStyle" :label="lang('password_confirmation')" :value="password_confirmation" type="password" name="password_confirmation"
                            :onChange="onChange" placehold="Confirm Password" :keyupListener="triggerEvent" id="password_confirmation"
                            :required="true">

                        </text-field>

                        <div class="social-auth-links text-center mb-1">

                            <a href="javascript:;" class="btn btn-block btn-primary" @click="onSubmit()">

                                <i class="fas fa-sync"></i>&nbsp;&nbsp;{{lang('reset_password')}}
                            </a>
                        </div>

                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { mapGetters } from 'vuex';
    import { errorHandler, successHandler } from 'helpers/responseHandler'
    import { validateResetSettings } from "helpers/validator/resetRules";
    import axios from 'axios'
    export default {
        name: 'reset',
        data() {
            return {
                email: '',
                password: '',
                password_confirmation:'',
                path:'',
                labelStyle: { display: 'none' },
                token:'',
                userEmail : '',
                loading: false,
            }
        },
        beforeMount() {
            if (this.getUserToken) {
                this.$router.push({ name: 'Login' }).catch(err => { })
            }
        },
        mounted(){
            this.userEmail = location.search.split('=');
            this.user = decodeURIComponent(this.userEmail[this.userEmail.length-1]);
            this.loading = false;
        },
        computed: {
            ...mapGetters(['getUserToken'])
        },
        methods: {
            onChange(value, name) {
                this[name] = value;
            },
            isValid() {
                const { errors, isValid } = validateResetSettings(this.$data);
                return isValid;
            },
            triggerEvent(event) {
                var key = event.which || event.keyCode;
                if (key === 13) { // 13 is enter
                    this.onSubmit();
                }
            },
            onSubmit() {
                if(this.isValid()){
                    if(this.password === this.password_confirmation){
                        this.loading = true;
                        this.path= location.pathname.split('/');
                        this.token = this.path[this.path.length-1];
                        const data = {token : this.token, password : this.password_confirmation}
                        data['email'] = this.email
                        data['password'] = this.password
                        data['password_confirmation'] = this.password_confirmation
                        axios.post('api/reset',data).then(res=>{
                            this.loading = false;
                            successHandler(res,'reset');
                            setTimeout(()=>{
                                this.$router.push({ path:'/login/',name: 'login'});
                            },3000)
                        }).catch(error=>{
                            errorHandler(error,'reset');
                            this.loading = false;
                        })
                    }
                    else {
                        this.$store.dispatch('setValidationError', {'password_confirmation' : 'Password does not match'})
                    }
                }
            }
            // onSubmit() {
            //
            //     if(this.isValid()){
            //
            //         if(this.password === this.repeat){
            //
            //             this.$Progress.start();
            //
            //             this.loading = true;
            //
            //              this.token = this.path[this.path.length-1];
            //
            //             this.token = this.path[this.path.length-1];
            //
            //             const data = {token : this.token, password : this.repeat}
            //
            //             axios.post('/api/reset/' + this.token,data).then(res=>{
            //
            //                 console.log(data)
            //
            //                 this.loading = false;
            //
            //                 successHandler(res,'reset');
            //
            //                 this.$Progress.finish();
            //
            //                 setTimeout(()=>{
            //
            //                     this.$router.push({ path:'/login',name: 'Login'});
            //                 },3000)
            //
            //             }).catch(error=>{
            //
            //                 errorHandler(error,'reset');
            //
            //                 this.$Progress.fail();
            //
            //                 this.loading = false;
            //             })
            //         } else {
            //
            //             this.$store.dispatch('setValidationError', {'repeat' : 'Password does not match'})
            //         }
            //     }
            // }
        },
        components: {
            "text-field": require("components/Reusable/FormField/TextField").default,
        }
    };
</script>
<template>
    <div class="col-sm-12">
        <alert componentName="DebugSettings" />
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">Debugger Settings</h3>
            </div>

            <div class="card-body">

                <div class="row" v-if="loading">

                    <custom-loader :duration="4000"></custom-loader>
                </div>
                <div class="row">
                    <div class="col-6">
                        <input type="radio" value="1" v-model="selectedValue" />
              &nbsp;<label class="ml-1"> Enable </label> &emsp;

              <input type="radio" value="0" v-model="selectedValue" />
            &nbsp;<label class="ml-1">
              Disable
            </label>
                    </div>
                    <div class="col-6 d-flex justify-content-start">
                        <div v-if="showLink" class="d-flex gap-3">
                            <a :href="basePath() + '/clockwork/app?user_id=' + user_id" class="btn btn-outline-info btn-block btn-flat">
                                <i class="fa-solid fa-clock fa-spin"></i>&nbsp; Clockwork
                            </a>
                            <a :href="basePath() + '/pulse'" class="btn btn-outline-danger btn-block btn-flat">
                                <i class="fa-solid fa-heart-pulse fa-beat"></i>&nbsp; Pulse
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" @click="saveValue">
                    <i :class="iconClass"></i>&nbsp;&nbsp;{{ lang(btnName) }}
                </button>
            </div>
        </div>
    </div>
</template>


<script>
import axios from "axios";
import { useStore } from "vuex";
import { computed } from "vue";
import { errorHandler ,successHandler} from '../../helpers/responseHandler';

export default {
    name: 'DebugSettings',
    setup() {
        const store = useStore();
        return {
            getUserId: computed(() => store.getters.getUserData),
        };
    },
    data() {
        return {
            selectedValue: "0",
            debugValue: "",
            iconClass: "fas fa-save",
            btnName: "save",
            isLoggedIn: true,
            showLink: false,
            user_id: 0,
            getValue: "",
            loading: false
        };
    },
    beforeMount() {
        this.fetchDebugger();
    },
    created() {
        this.showLink = false;
        this.user_id = this.getUserId.client_id || 0;
        this.saveTokenForDebugger();
    },


    mounted() {
        window.addEventListener("beforeunload", this.saveToLocalStorage);
    },

    methods: {
        fetchDebugger() {
            axios.get('/api/admin/getDebugger')
                .then((res) => {
                    this.debugValue = res.data.debugger;
                    this.selectedValue = this.debugValue
                    this.showLink = this.selectedValue === 1;
                })
                .catch((error) => {
                    errorHandler(error, 'DebugSettings');
                });
        },
        setFormData() {
            const emailSettings = this.$store.getters["getEmailSettings"];

            if (emailSettings) {
                this.settingId = emailSettings.SETTING_ID ?? "new";

                this.debugValue = emailSettings.EMAIL_FROM_NAME ?? false;
            }
        },

        saveValue() {

            this.loading = true;
            const data = {
                debug: this.selectedValue,
                user_id: this.user_id,
            };

            axios.post("/api/save-debug-value", data)
            .then((response) => {
                    this.debugValue = response.data.debug;
                    successHandler(response, 'DebugSettings')
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                    errorHandler(error,'DebugSettings')

                });
        },

        saveTokenForDebugger() {
            const data = {
                getdebug: this.selectedValue,
                user_id: this.user_id,
                _token: document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            };
        },
    },
};
</script>
<style>
label:not(.form-check-label):not(.custom-file-label) {
    font-weight: 500;
    line-height: -1rem;
    font-size: 17px;
}</style>

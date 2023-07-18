<template>
    <div class="col-sm-12">
        <alert componentName="DebugSettings" />
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">Debugger Settings</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label>
                            <input type="radio" value="1" v-model="selectedValue" />
                            Enable &nbsp;&nbsp;&nbsp;
                        </label>
                        <label>
                            <input type="radio" value="0" v-model="selectedValue" />
                            Disable
                            <input type="hidden" name="user_id" v-model="user_id" />
                        </label>
                    </div>
                    <div class="col-6">
                        <div v-if="showLink">
                            <a :href="basePath() + '/clockwork/app?user_id=' + user_id" style="margin-left: 5px"><i
                                    class="fas fa-clock fa-spin fa-lg"></i>&nbsp;&nbsp;Clockwork</a>
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
            selectedValue: "",
            debugValue: "",
            iconClass: "fas fa-save",
            btnName: "save",
            isLoggedIn: true,
            showLink: false,
            user_id: 0,
            getValue: "",
        };
    },

    created() {
        this.debugValue = localStorage.getItem("debug") || "";
        this.selectedValue = this.debugValue || "0"; // Set the default value to "0" (Disable) if debugValue is empty
        this.showLink = localStorage.getItem("showLink") === "true" || false;
        this.user_id = this.getUserId.admin_id || 0;
        this.saveTokenForDebugger();
    },


    mounted() {
        window.addEventListener("beforeunload", this.saveToLocalStorage);
    },

    methods: {
        setFormData() {
            const emailSettings = this.$store.getters["getEmailSettings"];

            if (emailSettings) {
                this.settingId = emailSettings.SETTING_ID ?? "new";

                this.debugValue = emailSettings.EMAIL_FROM_NAME ?? false;
            }
        },

        saveToLocalStorage() {
            localStorage.setItem("debug", this.selectedValue);
        },

        saveValue() {
            const data = {
                debug: this.selectedValue,
                user_id: this.user_id,
            };

            axios.post("/api/save-debug-value", data)
            .then((response) => {
                    this.debugValue = response.data.debug;
                    localStorage.setItem("debug", this.debugValue);

                    this.showLink = this.selectedValue === "1";
                    localStorage.setItem("showLink", this.showLink);
                    successHandler(response, 'DebugSettings')
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch((error) => {
                    errorHandler(response,'DebugSettings')
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

<template>
    <div v-if="progressBarValue !== 0" id="app">
        <div>{{ progressBarValue }}%</div>
        <progress :value="progressBarValue" max="100"></progress>
    </div>
    <div class="wrapper">


        <nav-bar :user="getUserData"></nav-bar>
        <side-bar :user="getUserData"></side-bar>

        <div class="content-wrapper">
            <bread-crumbs></bread-crumbs>
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <router-view v-slot="{ Component }" :key="$route.fullPath">
                            <transition name="fade" mode="out-in">
                                <component :is="Component" />
                            </transition>
                        </router-view>
                    </div>
                </div>
            </div>
        </div>
        <license-footer :versioning="versioning"></license-footer>
    </div>
</template>

<script>

import { computed }  from 'vue';
import { useStore } from 'vuex';

import Navbar from "./Components/Navbar.vue";

import Sidebar from "./Components/Sidebar.vue";

import Breadcrumbs from "./Components/Breadcrumbs.vue";

import Footer from "./Components/Footer.vue";

export default {

    name : 'license-manager-layout',

    props:{
        versioning : { type : String , default : ''},
    },

    setup() {

        const store = useStore();

        return {
            // getter
            getUserData: computed(() => store.getters.getUserData)

        };
    },

    computed: {
        progressBarValue() {
            return this.$store.state.progressBarValue;
        },
    },
    created() {
        const store = this.$store;

        // Create a variable to track the number of active requests
        let activeRequests = 0;

        // Start the progress bar when a request is made
        axios.interceptors.request.use((config) => {
            if (activeRequests === 0) {
                // If no active requests, start the progress bar
                store.dispatch('updateProgressBar', 20); // Change 20 to update the progress value
            }
            activeRequests++;
            return config;
        });

        // Hide or complete the progress bar when a response is received
        axios.interceptors.response.use(
            (response) => {
                activeRequests--;
                if (activeRequests === 0) {
                    // If no active requests, hide or complete the progress bar
                    store.dispatch('updateProgressBar', 100); // Completed
                    // Add a delay to remove the progress bar after a short period (adjust the timeout as needed)
                    setTimeout(() => {
                        store.dispatch('updateProgressBar', 0); // Remove the progress bar
                    }, 500);
                }
                return response;
            },
            (error) => {
                activeRequests--;
                // Handle errors here if needed
                if (activeRequests === 0) {
                    // If no active requests, hide or complete the progress bar
                    store.dispatch('updateProgressBar', 100); // Completed even if there's an error
                    // Add a delay to remove the progress bar after a short period (adjust the timeout as needed)
                    setTimeout(() => {
                        store.dispatch('updateProgressBar', 0); // Remove the progress bar
                    }, 500);
                }
                return Promise.reject(error);
            }
        );
    },

    components : {

        'nav-bar' : Navbar,

        'side-bar' : Sidebar,

        'bread-crumbs' : Breadcrumbs,

        'license-footer' : Footer,
    }
};
</script>

<style scoped>

.fade-enter {
    opacity: 0;
}

.fade-enter-active {
    transition: opacity 0.2s ease;
}

.fade-leave {}

.fade-leave-active {
    transition: opacity 0.2s ease;
    opacity: 0;
}
.wrapper {
    position: relative; /* Add relative positioning to the wrapper */
}
#app {
    width: 100%; /* Ensure the container spans the entire width */
    padding: 20px; /* Adjust as needed */
    position: fixed;
    z-index: 1000;
}

progress {
    width: 100%; /* Make the progress bar fill the container width */
    height: 5px; /* Set the height of the progress bar */
    appearance: none; /* Remove default styles */
    background-color: #ccc; /* Set a background color */
}
</style>

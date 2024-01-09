<template>
    <div  v-if="shouldShowProgressBar"  class="progress  color-shift-progress-bar">
        <div class="progress-bar" role="progressbar" :style="{width: progressBarWidth}"></div>

    </div>
    <router-view :versioning="versioning"></router-view>
   </template>

   <script>

   export default {

    props:{
        versioning : { type : String , default : ''},
    },

       data() {
           return {
               shouldShowProgressBar: false,
           };
       },

    watch : {

    $route(to, from){

    this.$store.dispatch('unsetAlert');

    this.$store.dispatch('unsetValidationError');
    }
    },

    beforeMount(){

    this.$store.dispatch('setApiKey');
    },

       computed: {
           progressBarWidth() {
               return this.$store.state.progressBarValue + '%';
           },
       },
       created() {
           const store = this.$store;
           let activeRequests = 0;

           const showProgressBar = () => {
               if (activeRequests === 0) {
                   this.shouldShowProgressBar = true;
                   store.dispatch('updateProgressBar', 20);
               }
               activeRequests++;
           };

           const hideProgressBar = () => {
               activeRequests--;
               if (activeRequests === 0) {
                   store.dispatch('updateProgressBar', 100);
                   setTimeout(() => {
                       this.shouldShowProgressBar = false;
                       store.dispatch('updateProgressBar', 0);
                   }, 500);
               }
           };

           const onRequestSuccess = (response) => {
               hideProgressBar();
               return response;
           };

           const onRequestError = (error) => {
               hideProgressBar();
               return Promise.reject(error);
           };

           axios.interceptors.request.use((config) => {
               showProgressBar();
               return config;
           });

           axios.interceptors.response.use(onRequestSuccess, onRequestError);
       },

   }
   </script>
<style scoped>
loader {
    width: 100%;
    position: fixed;
    top: 0;
    left: 0; /* Set to 0 to ensure the loader is positioned at the left */
    z-index: 1000;
    padding: 0;
    margin: 0;
}

progress {
    background: none;
    width: 100%; /* Make the progress bar fill the container width */
    height: 5px; /* Set the height of the progress bar */
}

.color-shift-progress-bar {
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, transparent 0, #00e1ff 200px, transparent 0);
    background-size: 200% 10px;
    animation: color-move-animation 2s linear infinite;
}

@keyframes color-move-animation {
    0% {
        background-position: 100% 0;
    }
    100% {
        background-position: -100% 0;
    }
}
</style>

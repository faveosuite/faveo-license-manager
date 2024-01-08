<template>
    <div  v-if="progressBarValue !== 0"  class="progress  color-shift-progress-bar" style="height: 3px;">
        <div class="progress-bar" role="progressbar" :style="{width: progressBarValue+'%'}"></div>

    </div>
    <router-view :versioning="versioning"></router-view>
   </template>

   <script>

   export default {

    props:{
        versioning : { type : String , default : ''},
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
           progressBarValue() {
               return this.$store.state.progressBarValue;
           },
       },
       created() {
           const store = this.$store;

           let activeRequests = 0;

           axios.interceptors.request.use((config) => {
               if (activeRequests === 0) {
                   store.dispatch('updateProgressBar', 20); // Change 20 to update the initial progress value
               }
               activeRequests++;
               return config;
           });

           // Hide or complete the progress bar when a response is received
           axios.interceptors.response.use(
               (response) => {
                   activeRequests--;
                   if (activeRequests === 0) {
                       store.dispatch('updateProgressBar', 100);
                       setTimeout(() => {
                           store.dispatch('updateProgressBar', 0); // Remove the progress bar
                       }, 500);
                   }
                   return response;
               },
               (error) => {
                   activeRequests--;
                   if (activeRequests === 0) {
                       store.dispatch('updateProgressBar', 100); // Completed even if there's an error
                       setTimeout(() => {
                           store.dispatch('updateProgressBar', 0); // Remove the progress bar
                       }, 500);
                   }
                   return Promise.reject(error);
               }
           );
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
    height: 2px; /* Set the height of the progress bar */
}

.color-shift-progress-bar {
    width: 100%;
    height: 5px;
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

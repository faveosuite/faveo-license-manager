<template>
    <router-view></router-view>
   </template>
   
   <script>
   import { useStore } from "vuex";
   import { computed } from "vue";

   export default {
    setup() {
    const store = useStore();
   
    return {
    getUserData: computed(() => store.getters.getUserData.client_id),
    getUserToken: computed(() => store.getters.getUserToken),
    };
    },
    watch: {
    $route(to, from) {
    this.$store.dispatch("unsetAlert");
    this.$store.dispatch("unsetValidationError");
    },
    },
    
    beforeMount() {
    this.$store.dispatch("setApiKey");
    this. liveApi();
    },
    methods: {
    liveApi() {
    const client_id = this.getUserData;   
    axios
    .get(`/api/admin/liveapi/${client_id}`)
    .then((res) => {
    if (res.data.data.logout === true) {
    this.getUserToken = null;
    axios
    .post("/api/admin/logout/" + this.getUserData.client_id)
    .then((res) => {
    this.$store.dispatch("setLoggedInUserToken", "");
   
    this.$store.dispatch("setUserInfo", "");
   
    this.loading = false;
   
    this.$router
    .push("/login")
   
    .catch((err) => {
    errorHandler(err);
   
    this.loading = false;
    });
    });
    } else {
    }
    })
    .catch((error) => {
    console.error("Error while calling live API", error);
    });
    },
 
    },
   };
   </script>
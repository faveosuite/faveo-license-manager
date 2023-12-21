<template>

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
   }
   </script>

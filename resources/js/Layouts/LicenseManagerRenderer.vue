<template>

    <router-view :version="version"></router-view>
   </template>

   <script>

   export default {

    props:{
        version : { type : String , default : ''},
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

<template>
  
  <router-view></router-view>
</template>

<script>
  
  export default {

    watch : {
      
      $route(to, from){
        
        this.$store.dispatch('unsetAlert');
        
        this.$store.dispatch('unsetValidationError');
      }
    },
  }
</script>
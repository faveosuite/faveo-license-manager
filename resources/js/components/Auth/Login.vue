<template>

  <div class="login-page">

  </div>
</template>
<script>

import { mapGetters } from 'vuex';

export default {

  name : 'Login',
  
  data () {

    return {

      admin_email : '',

      admin_password : '',

      token : '',

      userId : '',

      errorMessage : '',

    }
  },

  beforeMount() {

    if(this.getUserToken){
      
      this.$router.push({name:'Dashboard'})
    }
  },

  computed : {

    ...mapGetters(['getUserToken'])
  },

  methods : {
    
    loginUser() {
        
      let data = {}
          
      data['admin_email'] = this.admin_email
      
      data['admin_password'] = this.admin_password
      
      axios.post("public/api/login",data).then((res) => {
           
        this.$store.dispatch('setLoggedInUserToken',res.data.data.token);
        
        this.$store.dispatch('setUserInfo',res.data.data.user);
        
        this.$router.push('dashboard')

      }).catch((err) => {
            
        this.errorMessage = err.response.data.message
      });
    }
  }
};
</script>
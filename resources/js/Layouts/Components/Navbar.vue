<template>

	<nav class="main-header navbar navbar-expand navbar-white navbar-light">

		<ul class="navbar-nav">

	  		<li class="nav-item">

				<a class="nav-link" data-widget="pushmenu" href="javascript:;" role="button"><i class="fas fa-bars"></i></a>
	  		</li>

	  		<li class="nav-item d-none d-sm-inline-block">

				<router-link to="/dashboard" class="nav-link">{{trans('home')}}</router-link>
	  		</li>

		</ul>

		<ul class="navbar-nav ml-auto" v-if="user">

			<li class="nav-item user-menu">

        		<a class="nav-link" data-toggle="dropdown" aria-expanded="true">

          			<img :src="basePath()+'/themes/default/img/avatar5.png'" class="user-image img-circle elevation-2"
          				alt="User Image">

          			<span class="d-none d-md-inline">{{user.client_fname + ' ' + user.client_lname}}</span>
        		</a>
      		</li>

      		<li class="nav-item">

		        <a class="nav-link" href="javascript:;" role="button" v-tooltip="trans('sign_out')" @click="signOut()">

		          	<i class="fas fa-power-off"></i>
		        </a>
		    </li>
		</ul>

	    <custom-loader v-if="loading"></custom-loader>
  	</nav>
</template>

<script>

	import { errorHandler } from '../../helpers/responseHandler';

	export default {

		name : 'nav-bar',

		props : {

			user : { type : [Object, String], default : ''}
		},

		data () {

			return {

				loading : false
			}
		},

		methods : {

			signOut() {

				this.loading = true;

				axios.post('/api/admin/logout/'+this.user.client_id).then(res=>{

					this.$store.dispatch('setLoggedInUserToken','');

          			this.$store.dispatch('setUserInfo','');

          			this.loading = false;

          			this.$router.push('/login')

				}).catch(err=>{

					errorHandler(err);

          			this.loading = false;

				})
			}
		}
	}
</script>


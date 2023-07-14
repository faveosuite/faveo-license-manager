<template>

	<aside class="main-sidebar sidebar-dark-secondary elevation-4">

    	<a href="javascript:;" class="brand-link text-center">

            <a>Agora License Manager</a>

    	</a>

    	<div class="sidebar" :key="counter">

      		<div class="user-panel mt-3 pb-3 mb-3 d-flex" v-if="user">

        		<div class="image">

          			<img :src="basePath()+'/themes/default/img/avatar5.png'" class="img-circle elevation-2" alt="User Image">
        		</div>

        		<div class="info">

          			<a href="javascript:;" class="d-block" v-tooltip="user.admin_fname + ' ' + user.admin_lname">
          				{{subString(user.admin_fname + ' ' + user.admin_lname)}}
          			</a>
        		</div>
      		</div>

      		<nav class="mt-2">

        		<div v-if="loading" class="license-navigation">

					<loader :size="40"></loader>
				</div>

				<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
	                role="menu" data-accordion="true">

	                <navigation v-for="(navigation, index) in navigations" :menuItem="navigation" :key="index">

	                </navigation>
	            </ul>
      		</nav>
     	</div>
    </aside>
</template>

<script>

	import axios from 'axios';

	import { getSubStringValue } from '../../helpers/extraLogics'

    import Loader from "../../components/Reusable/Loader.vue";

    import Navigation from "./Navigation.vue";

	export default {

		name : 'side-bar',

		props : {

			user : { type : [Object, String], default : ''}
		},

		data () {

			return {

				navigations : [],

				loading : true,

				active : false,

				counter : 0
			}
		},

		beforeMount () {

			this.getRoutes();
		},

		watch : {

			$route(to, from){

        		this.counter += 1;
		   	}
		},

		methods : {

			subString(value,length = 15){

				return getSubStringValue(value,length)
			},

			getRoutes() {

          		axios.get('/json/routes.json').then((response) => {

            		setTimeout(()=>{

            			this.loading = false;

            			this.navigations = response.data.navigations;

            		},1000);

          		}).catch((error) => {

            		this.loading = false;
          		})
        	}
		},

		components : {

			'loader': Loader,

			'navigation': Navigation,
		}
	}
</script>

<style scoped>

	.license-navigation { margin-top : 200px !important;}
</style>

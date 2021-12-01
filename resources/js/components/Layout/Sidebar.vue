<template>
	
	<aside class="main-sidebar sidebar-dark-primary elevation-4">
    
    	<a href="javascript:;" class="brand-link text-center">

    		<img :src="basePath()+'/themes/default/img/logo.png'" alt="Faveo Logo" class="brand-image ml-0 float-none">
    	</a>

    	<div class="sidebar" :key="counter">
      		
      		<div class="user-panel mt-3 pb-3 mb-3 d-flex">
        		
        		<div class="image">
          		
          			<img :src="basePath()+'/themes/default/img/user2-160x160.jpg'" class="img-circle elevation-2" alt="User Image">
        		</div>
        		
        		<div class="info">
          			
          			<a href="javascript:;" class="d-block">Alexander Pierce</a>
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

	export default {

		name : 'side-bar',

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

			'loader': require('components/Reusable/Loader').default,

			'navigation': require('./Navigation').default,
		}
	}
</script>

<style scoped>
	
	.license-navigation { margin-top : 200px !important;}
</style>
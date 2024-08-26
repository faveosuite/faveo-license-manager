<template>

	<aside class="main-sidebar sidebar-scroll sidebar-dark-secondary elevation-4">

    	<a href="javascript:;" class="brand-link text-center">

            <image-element id="admin-pic" width="100px" height="100px" :classes="['img-responsive', 'img-circle', 'img-click', 'custom-img']" :sourceUrl="getAdminLogo"></image-element>
    	</a>

    	<div class="sidebar" :key="counter">

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
    import ImageElement from "../../components/Reusable/ImageElement.vue";
    import {useStore} from "vuex";
    import {computed} from "vue";

	export default {

		name : 'side-bar',

        setup() {

            const store = useStore();

            return {

                getAdminLogo : computed(() => store.getters.getAdminData)
            }
        },

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
            "image-element": ImageElement,

			'loader': Loader,

			'navigation': Navigation,
		}
	}
</script>

<style scoped>

	.license-navigation { margin-top : 200px !important;}

    .user-panel img{
        height: 2.1rem;
        width: 2.1rem;
        object-fit: cover;
    }
    .sidebar-scroll{
        max-height: 100vh!important;
        bottom: 0!important;
    }
</style>

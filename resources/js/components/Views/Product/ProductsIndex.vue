<template>
		
	<div class="col-sm-12">
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title">{{lang('products')}}</h3>

				<div class="card-tools">
					
					<router-link to="/products/create" class="btn-tool" v-tooltip="lang('create_product')">
          
          			<i class="fas fa-plus"></i>
        			</router-link>
				</div>
			</div>
			
			<div class="card-body" id="my_products">
			
				<!-- <data-table :url="apiUrl" :dataColumns="columns"  :option="options" scroll_to="canned_response"></data-table> -->
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	export default {

		name : 'products-list',

		data() {

			return {

				columns: ['title', 'created_at', 'updated_at', 'action'],

				options: {},

				apiUrl:'/api/admin/viewproducts',
			}
		},

		beforeMount(){

			const self= this;
		}
	};
</script>
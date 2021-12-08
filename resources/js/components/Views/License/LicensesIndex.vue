<template>
		
	<div class="col-sm-12">
		
		<alert componentName="dataTableModal"/>

		<div class="card card-light ">
		
			<div class="card-header">
			
				<h3 class="card-title">{{lang('licenses')}}</h3>

				<div class="card-tools">
					
					<router-link to="/licenses/create" class="btn-tool" v-tooltip="lang('create_license')">
          
          			<i class="fas fa-plus"></i>
        			</router-link>
				</div>
			</div>
			
			<div class="card-body" id="my_licenses">

				<v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

				</v-client-table>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios';

	export default {

		name : 'licenses-list',

		data() {

			return {

				data : '',

				columns: ['product_id', 'license_code', 'total_installations', 'license_status', 'actions'],

				options: {},

				counter : 0
			}
		},

		created() {
		
			window.eventHub.$on('refreshData',this.updateData);
		},

		beforeMount(){

			const self= this;

			this.getData();

			this.options = {

				sortIcon: {
						
					base : 'glyphicon',
						
					up: 'glyphicon-chevron-up',
						
					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				columnsClasses : {

		          	product_id : 'license_product_id',

		          	license_code: 'license_code',

		         	total_installations : 'license_install',

		         	license_status: 'license_status'
		        },

		        templates : {

					license_status(createElement, row) {
			          	
			          	let span = createElement('span', {
			             	
			             	attrs: {
			               		'class' : row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
			             	}
			          	}, row.license_status ? 'Active' : 'Inactive');
			          	
			          	return createElement('a',{},[span]);
			        },

			        actions : 'table-actions'
		        },

				pagination:{chunk:5,nav: 'fixed',edge:true},

				headings: {
			        
			        product_id: 'Product',
			        
			        license_code: 'License Code',
			   		
			   		total_installations: 'Installations',

			   		license_status: 'Status',

			   		actions: 'Actions'
			    },
			}
		},

		methods : {

			updateData() {

				this.getData();
			},

			getData() {

				axios.get('/api/admin/viewLicenses').then(res=>{

					this.data = res.data.data.map(data => {

						data.edit_url = '/licenses/' + data.license_id + '/edit';

						data.delete_url = '/api/admin/license/delete';

						data.keyVal = 'license_id';

						data.idVal = data.license_id;

						return data;
					})
				})
			}
		}
	};
</script>

<style>
	
	.license_name,.license_email,.license_date,.license_status{ max-width: 200px; word-break: break-all;}
	
	#my_licenses .VueTables .table-responsive {
		overflow-x: auto;overflow-y: hidden;
	}

	#my_licenses .VueTables .table-responsive > table{
		width : max-content;
		min-width : 100%;
		max-width : max-content;
		overflow: auto !important;
	}
</style>
<template>

	<div class="actions-row">

		<router-link v-if="data.edit_url" class="btn btn-default" :to="data.edit_url" v-tooltip="trans('edit')">

			<i class="fas fa-edit"></i>
		</router-link>

		<router-link v-if="data.view_url" class="btn btn-default" :to="data.view_url" v-tooltip="trans('view')">
	
			<i class="fas fa-eye"></i>
		</router-link>

		<span v-tooltip="disabled ? trans('default_field_is_not_deletable') : trans('delete')">

			<button v-if="data.delete_url" class="btn btn-default" @click="showModalMethod"
				:disabled="disabled">

				<i class="fas fa-trash"></i>
			</button>
		</span>

		<transition name="modal">

		 	<delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url" 
		 		:alertComponentName="alert">
		 		
		 	</delete-modal>
		</transition> 
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';
	
	import {boolean} from 'helpers/extraLogics'
	
	export default {
	
		name:"data-table-actions",
	
		props: {
	
			data : { type : Object, required : true },
		},
	
		data(){
	
			return{
	
				showModal : false,
	
				alert : ''
			}
		},

		computed : {

			disabled() {

				return boolean(this.data.is_default)
			}
		},

		created() {
			
			this.updateAlert()
		},

		methods:{

			updateAlert() {

				this.alert = this.data.alertComponentName ? this.data.alertComponentName : 'dataTableModal'; 
			},

			showModalMethod(){

				this.showModal = this.data.is_default ? false : true;
			},

			onClose(){
		    	
		    	this.showModal = false;
		    	
		    	this.$store.dispatch('unsetValidationError');
		  	},
		},
		
		components:{
		
			'delete-modal': require('./DeleteModal'),
		}
	};
</script>

<style scoped>
	
	.actions-row a { padding-right: 10px;padding-left: 10px; }
</style>


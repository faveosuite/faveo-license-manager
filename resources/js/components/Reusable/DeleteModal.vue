<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
			
			<h4 class="modal-title">{{trans('delete')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">
	
			<span v-else>{{trans('are_you_sure_you_want_to_delete_dependencies')}}</span>
		</div>

		<div slot="alert">
			
			<alert componentName="delete-modal"></alert>	
		</div>

		<div v-if="loading" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>

		<div slot="controls">
			
			<button type="button" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

				<i class="fas fa-trash" aria-hidden="true"></i> {{trans('delete')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	export default {

		name : 'delete-modal',

		description : 'Delete Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			deleteUrl:{type:String},

			onClose:{type: Function},

			alertComponentName : { type : String, default : 'dataTableModal'},

			componentTitle : { type : String, default : ''}

		},

		data () {

			return {

				containerStyle : { width:'650px' },

				loading:false,

				isDisabled : false,

				labelStyle : { display:'none' },

				apiUrl : this.deleteUrl
			}
		},

		methods:{

			onSubmit(){
				
				this.loading = true

				this.isDisabled = true;
				
				axios.delete(this.apiUrl).then(res=>{

					successHandler(res,this.alertComponentName);

					this.afterRespond();
				
				}).catch(err => {

					errorHandler(err,'delete-modal');

					this.loading = false;

					this.isDisabled = false;	
				})
			},

			afterRespond(){

				window.eventHub.$emit(this.componentTitle+'refreshData');

				window.eventHub.$emit(this.componentTitle+'uncheckCheckbox');

				this.onClose();

				this.loading = false;

				this.isDisabled = false;
			}
		}
	};
</script>

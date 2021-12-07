<template>
	
	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

		<div slot="title">
			
			<h4 class="modal-title">{{trans('delte')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">
	
			<span>{{trans('are_you_sure')}}</span>
		</div>

		<div slot="alert">
			
			<alert componentName="delete-modal"></alert>	
		</div>

		<div v-if="loading" slot="fields" >
			
			<loader :animation-duration="4000" color="#1d78ff" :size="60"/>
		</div>

		<div slot="controls">
			
			<button type="button" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

				<i class="fas fa-trash" aria-hidden="true"></i> {{trans('delte')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">
	
	import axios from 'axios'

	import {errorHandler, successHandler} from 'helpers/responseHandler'

	import { mapGetters } from 'vuex';

	export default {

		name : 'delete-modal',

		description : 'Delete Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			deleteUrl:{type:String},

			onClose:{type: Function},

			alertComponentName : { type : String, default : 'dataTableModal'},

			componentTitle : { type : String, default : ''},

			keyVal : {type : String, default : ''},

			idVal : { type : String | Number, default : '' }

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

		computed : {

			...mapGetters(['getApiKey'])
		},

		methods:{

			onSubmit(){
				
				this.loading = true

				this.isDisabled = true;

				const data = {};

				data[this.keyVal] = this.idVal;

				data['api_key_secret']= this.getApiKey;
				
				axios.post(this.apiUrl,data).then(res=>{

					successHandler(res,this.alertComponentName);

					this.afterRespond();
				
				}).catch(err => {

					errorHandler(err,'delete-modal');

					this.loading = false;

					this.isDisabled = false;	
				})
			},

			afterRespond(){

				window.eventHub.$emit('refreshData');

				this.onClose();

				this.loading = false;

				this.isDisabled = false;
			}
		}
	};
</script>

<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

        <template v-slot:title>

            <div>

                <h4 class="modal-title">{{trans('delte')}}</h4>
            </div>
        </template>

        <template v-slot:fields>

            <div v-if="loading" class="mt-5 mb-5">

                <loader :animation-duration="4000" color="#1d78ff" :size="60"/>
            </div>

            <div v-if="!loading">

                <span>{{trans('are_you_sure')}}</span>
            </div>
        </template>

        <template v-slot:alert>

            <div>

                <alert componentName="delete-modal"></alert>
            </div>
        </template>

        <template v-slot:controls>

            <div>

                <button type="button" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled">

                    <i class="fas fa-trash" aria-hidden="true"></i> {{trans('delte')}}
                </button>
            </div>
        </template>
	</modal>
</template>

<script type="text/javascript">

	import axios from 'axios'

	import {errorHandler, successHandler} from '../../helpers/responseHandler'

	export default {

		name : 'delete-modal',

		description : 'Delete Modal component',

		props:{

			showModal:{type:Boolean,default:false},

			deleteUrl:{type:String},

			onClose:{type: Function},

			alertComponentName : { type : String, default : 'dataTableModal'},

            redirectUrl : { type : String, default : ''},

			componentTitle : { type : String, default : ''},

			keyVal : {type : String, default : ''},

			idVal : { type : [String, Number], default : '' }

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

                if(this.redirectUrl){

                    setTimeout(()=>{
                        this.$router.push({ path : this.redirectUrl })
                    },3000);
                }

                window.emitter.emit('refreshData')

				this.onClose();

				this.loading = false;

				this.isDisabled = false;
			}
		}
	};
</script>

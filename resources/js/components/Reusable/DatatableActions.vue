<template>

	<div class="actions-row">

		<router-link v-if="data.edit_url" class="btn btn-default btn-act" :to="data.edit_url" v-tooltip="trans('edit')">

			<i class="fas fa-edit"></i>
		</router-link> &nbsp;

		<span v-tooltip="disabled ? trans('default_field_is_not_deletable') : trans('delte')">

			<button v-if="data.delete_url" class="btn btn-default btn-act" @click="showModalMethod"
				:disabled="disabled">

				<i class="fas fa-trash"></i>
			</button>
		</span>

		<transition name="modal">

		 	<delete-modal v-if="showModal" :onClose="onClose" :showModal="showModal" :deleteUrl="data.delete_url"
		 		:alertComponentName="alert" :keyVal="data.keyVal" :idVal="data.idVal">

		 	</delete-modal>
		</transition>
	</div>
</template>

<script type="text/javascript">

	import axios from 'axios';

	import {boolean} from '../../helpers/extraLogics'

    import DeleteModal from './DeleteModal.vue'

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

			'delete-modal': DeleteModal
		}
	};
</script>

<style scoped>

	.actions-row a { padding-right: 10px;padding-left: 10px; }

	.btn-act { background: gainsboro !important; }
</style>


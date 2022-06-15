<template>

	<modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle" modalBodyClass="body-scrollable">

		<div slot="title">

			<h4 class="modal-title">{{trans('response')}}</h4>
		</div>

		<div v-if="!loading" slot="fields">

			<span>{{ responseData}}</span>
		</div>

		<div slot="alert">

			<alert componentName="response-modal"></alert>
		</div>

		<div v-if="loading" slot="fields">

			<loader :animation-duration="4000" color="#1d78ff" :size="60" />
		</div>

		<div slot="controls">

			<button type="button" @click="copyMethod" class="btn btn-default" :disabled="isDisabled">

				<i class="fas fa-copy" aria-hidden="true"></i> {{trans('copy')}}
			</button>
		</div>
	</modal>
</template>

<script type="text/javascript">


	import {successHandler} from "../../helpers/responseHandler";

    export default {

		name: 'response-modal',

		description: 'Response Modal component',

		props: {

			showModal: { type: Boolean, default: false },

			onClose: { type: Function },

			componentTitle: { type: String, default: '' },

			responseData: { type: Object | String | Number, default: '' },

		},

		data() {

			return {

				containerStyle: { width: '650px' },

				loading: false,

				isDisabled: false,

				labelStyle: { display: 'none' },

				apiUrl: this.deleteUrl
			}
		},


		methods: {

			stringify(value) {
				switch (typeof value) {
					case 'string': case 'object': return JSON.stringify(value);
					default: return String(value);
				}
			},
			copyMethod() {
				let inputElem = document.createElement("input");
				inputElem.type = "text";
				// inputElem.hidden = true;
				inputElem.value = this.stringify(this.responseData);
				document.body.appendChild(inputElem);
				inputElem.select();
				document.execCommand("Copy");
				document.body.removeChild(inputElem);
                successHandler({
                    status: 200,
                    data: {
                        message: 'Response has been copied to clipboard'
                    }
                }, 'configuration');
				this.onClose()

			},


		}
	};
</script>

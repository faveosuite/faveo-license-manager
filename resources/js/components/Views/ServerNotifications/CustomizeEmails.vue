<template>

	<div class="col-sm-12">

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="custom-email" />

		<div class="card card-light" v-if="hasDataPopulated">

			<div class="card-header">

				<h3 class="card-title">{{trans('customize_emails')}}</h3>
			</div>

			<div class="card-body">

				<div class="row">

					<text-field :label="trans('email_expiring_license_subject')" :value="email_expiring_license_subject"
						type="textarea" name="email_expiring_license_subject" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>

					<text-field :label="trans('email_expiring_license_text')" :value="email_expiring_license_text"
						type="textarea" name="email_expiring_license_text" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>
				</div>

				<div class="row">

					<text-field :label="trans('email_expiring_updates_subject')" :value="email_expiring_updates_subject"
						type="textarea" name="email_expiring_updates_subject" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>

					<text-field :label="trans('email_expiring_updates_text')" :value="email_expiring_updates_text"
						type="textarea" name="email_expiring_updates_text" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>
				</div>

				<div class="row">

					<text-field :label="trans('email_expiring_support_subject')" :value="email_expiring_support_subject"
						type="textarea" name="email_expiring_support_subject" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>

					<text-field :label="trans('email_expiring_support_text')" :value="email_expiring_support_text"
						type="textarea" name="email_expiring_support_text" :onChange="onChange" classname="col-sm-6"
						:required="true">

					</text-field>
				</div>
			</div>

			<div class="card-footer">

				<button class="btn btn-default" @click="onSubmit()"><i
						class="fas fa-sync"></i>&nbsp;&nbsp;{{trans('update')}}</button>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import { validateCustomEmailSettings } from "helpers/validator/customEmailValidation.js";

	import { mapGetters } from 'vuex';

	export default {

		name: 'customize-emails',

		data() {

			return {

				hasDataPopulated: false,

				loading: false,

				email_id: '',

				email_expiring_license_subject: '',

				email_expiring_license_text: '',

				email_expiring_updates_subject: '',

				email_expiring_updates_text: '',

				email_expiring_support_subject: '',

				email_expiring_support_text: '',
			}
		},

		beforeMount() {

			this.getInitialValues();
		},

		computed: {

			...mapGetters(['getApiKey'])
		},

		methods: {

			getInitialValues() {

				this.loading = true

				axios.get('/api/admin/viewEmails').then(res => {

					this.loading = false;

					this.hasDataPopulated = true;

					let resData = res.data.data[0];

					this.updateStatesWithData(resData);

				}).catch(error => {

					this.loading = false;
				});
			},

			updateStatesWithData(data) {

				const self = this;

				const stateData = this.$data;

				Object.keys(data).map(key => {

					if (stateData.hasOwnProperty(key)) {

						self[key] = data[key];
					}
				});
			},

			isValid() {

				const { errors, isValid } = validateCustomEmailSettings(this.$data);

				return isValid;
			},

			onChange(value, name) {

				this[name] = value ? value : '';
			},

			onSubmit() {

				if (this.isValid()) {

					this.loading = true

					const data = {};

					data['api_key_secret'] = this.getApiKey;

					data['email_expiring_license_subject'] = this.email_expiring_license_subject;

					data['email_expiring_license_text'] = this.email_expiring_license_text;

					data['email_expiring_updates_subject'] = this.email_expiring_updates_subject;

					data['email_expiring_updates_text'] = this.email_expiring_updates_text;

					data['email_expiring_support_subject'] = this.email_expiring_support_subject;

					data['email_expiring_support_text'] = this.email_expiring_support_text;

					axios.post('/api/admin/emails/' + this.email_id, data).then(res => {

						this.loading = false

						successHandler(res, 'custom-email');

						this.getInitialValues();

					}).catch(err => {

						this.loading = false

						errorHandler(err, 'custom-email')
					});
				}
			}
		},

		components: {

			"text-field": require("components/Reusable/FormField/TextField").default,
		}
	}
</script>
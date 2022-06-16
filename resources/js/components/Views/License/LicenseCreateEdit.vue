<template>

	<div class="col-sm-12">

		<div class="alert alert-info">
			<p>Add new license to be used. It's possible to add licenses with and without client's profile.<br><br>With
				client's profile (when client's name and email address are known): select client and product from the
				list, and click the 'Submit'
				button. Client will need to enter his email address during script installation to verify his
				license.<br><br>Without client's profile (when anonymous license needs to be issued): select product
				from the list and enter unique license
				code (entering <b>random</b> will automatically generate a random code). Client will need to enter this
				code during script installation to verify his license.<br><br>If IP address and/or domain is set,
				product will only work on
				specified IP and/or domain. If licensed domain is entered as clientdomain.com, product will work on
				clientdomain.com and clientdomain.com/any/directory. If licensed domain is entered as
				clientdomain.com/path, product will only
				work on clientdomain.com/path. It's possible to add multiple licensed IPs and/or domains by separating
				them with comma (,) symbol. If installations limit is set, client will not be able to run more copies of
				licensed product than
				specified number.<br><br>If expiration date is set, application will stop working after this date
				(expiration date can be updated at any time).</p>
		</div>

		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="license" />

		<div class="card card-light" v-if="hasDataPopulated">

			<div class="card-header">

				<h3 class="card-title">{{trans(title)}}</h3>
			</div>

			<div class="card-body">

				<div class="row">

					<dynamic-select :label="trans('product')" :multiple="false" :elements="productOptions"
						name="product_id" classname="col-sm-6" :value="product_id" :onChange="onChange" :strlength="35"
						:required="true">
					</dynamic-select>

					<dynamic-select :label="trans('client')" :multiple="false" :elements="clientOptions"
						name="client_id" classname="col-sm-6" :value="client_id" :onChange="onChange" :strlength="35"
						:required="false">
					</dynamic-select>
				</div>

				<div class="row">

					<text-field :label="trans('license_code')" :value="license_code" type="text" name="license_code"
						:onChange="onChange" classname="col-sm-6" :required="client_id ? false : true"
						:showNewButton="client_id ? false : true" newBtnName="generate" :onNewButtonClick="generateCode"
						:disabled="client_id ? true : false">

					</text-field>

					<number-field :label="trans('order_number')" :value="license_order_number"
						name="license_order_number" :onChange="onChange" classname="col-sm-6">

					</number-field>
				</div>

				<div class="row">

					<text-field :label="trans('licensed_ip')" :value="license_ip" type="text" name="license_ip"
						:onChange="onChange" classname="col-sm-6">

					</text-field>

					<dynamic-select :label="trans('licensed_domain')" :multiple="true" :elements="[]"
						name="license_domain" classname="col-sm-6" :value="license_domain" :onChange="onChange"
						:strlength="35" :required="false" :taggable="true" :hint="trans('domain_tip')">
					</dynamic-select>
				</div>

				<div class="row">

					<date-picker :label="trans('license_expire_date')" :value="license_expire_date" type="date"
						name="license_expire_date" :onChange="onChange" :required="false" format="DD-MM-YYYY"
						classname="col-sm-4" :clearable="true" :disabled="false" :confirm="false">

					</date-picker>

					<date-picker :label="trans('license_updates_date')" :value="license_updates_date" type="date"
						name="license_updates_date" :onChange="onChange" :required="false" format="DD-MM-YYYY"
						classname="col-sm-4" :clearable="true" :disabled="false" :confirm="false">

					</date-picker>

					<date-picker :label="trans('license_support_date')" :value="license_support_date" type="date"
						name="license_support_date" :onChange="onChange" :required="false" format="DD-MM-YYYY"
						classname="col-sm-4" :clearable="true" :disabled="false" :confirm="false">

					</date-picker>
				</div>

				<div class="row">

					<radio-button :options="domainOptions" :label="trans('license_require_domain')"
						name="license_require_domain" :value="license_require_domain" :onChange="onChange"
						classname="form-group col-sm-4">

					</radio-button>

					<radio-button :options="radioOptions" :label="trans('status')" name="license_status"
						:value="license_status" :onChange="onChange" classname="form-group col-sm-4">

					</radio-button>

					<number-field :label="trans('installations_limit')" :value="license_limit" name="license_limit"
						:onChange="onChange" classname="col-sm-4">

					</number-field>
				</div>

				<div class="row">

					<text-field :label="trans('comments')" :value="license_comments" type="textarea"
						name="license_comments" :onChange="onChange" classname="col-sm-12">

					</text-field>
				</div>
			</div>

			<div class="card-footer">

				<button class="btn btn-primary" @click="onSubmit()"><i
						:class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import { getIdFromUrl, generateRandomString } from 'helpers/extraLogics';

	import { validateLicenseSettings } from "helpers/validator/licenseValidation.js";

	import { mapGetters } from 'vuex';

	import moment from 'moment'

	export default {

		name: 'license-create-edit',

		data() {

			return {

				title: 'create_new_license',

				iconClass: 'fas fa-save',

				btnName: 'save',

				hasDataPopulated: false,

				loading: false,

				license_status: 1,

				radioOptions: [{ name: 'active', value: 1 }, { name: 'inactive', value: 0 }],

				license_require_domain: 1,

				domainOptions: [{ name: 'yes', value: 1 }, { name: 'no', value: 0 }],

				apiEndpoint: '',

				license_id: '',

				product_id: '',

				productOptions: [],

				client_id: '',

				clientOptions: [],

				license_code: '',

				license_order_number: '',

				license_ip: '',

				license_domain: '',

				license_limit: '',

				license_expire_date: '',

				license_updates_date: '',

				license_support_date: '',

				license_comments: '',

				moment: moment
			}
		},

		beforeMount() {

			const path = window.location.pathname

			this.getValues(path);

			this.loadData();
		},

		computed: {

			...mapGetters(['getApiKey'])
		},

		methods: {

			loadData() {

				this.loading = true;

				this.hasDataPopulated = false;

				Promise.all([this.getProducts(), this.getClients()]).then((values) => {

					[this.productOptions, this.clientOptions] = values;

					this.loading = false;

					this.hasDataPopulated = true;

				}).catch(function (error) {

					this.loading = false;

					this.hasDataPopulated = true;
				});
			},

			getProducts() {

				axios.get('/api/admin/viewproducts').then(res => {

					this.productOptions = res.data.data.map(data => {

						data.name = data.product_title;

						data.id = data.product_id;

						return data;
					})
				});

				return this.productOptions
			},

			getClients() {

				axios.get('/api/admin/viewClients').then(res => {

					this.clientOptions = res.data.data.map(data => {

						data.name = data.client_fname + ' ' + data.client_lname;

						data.id = data.client_id;

						return data;
					})
				});

				return this.clientOptions
			},

			getValues(path) {

				const licenseId = getIdFromUrl(path)

				if (path.indexOf('edit') >= 0) {

					this.title = 'edit_license'

					this.iconClass = 'fas fa-sync'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(licenseId);

					this.license_id = licenseId;

					this.apiEndpoint = '/api/admin/license/edit';

				} else {

					this.loading = false;

					this.hasDataPopulated = true;

					this.apiEndpoint = '/api/admin/license/add';
				}
			},

			getInitialValues(id) {

				this.loading = true

				axios.get('/api/admin/license/' + id).then(res => {

					this.loading = false;

					this.hasDataPopulated = true;

					let resData = res.data.data.license;

					resData['license_domain'] = resData.license_domain ? resData.license_domain.split(',') : '';

					resData['license_expire_date'] = resData.license_expire_date ? new Date(moment(resData.license_expire_date).format("MM-DD-YYYY")) : '';

					resData['license_updates_date'] = resData.license_updates_date ? new Date(moment(resData.license_updates_date).format("MM-DD-YYYY")) : '';

					resData['license_support_date'] = resData.license_support_date ? new Date(moment(resData.license_support_date).format("MM-DD-YYYY")) : '';

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

				const { errors, isValid } = validateLicenseSettings(this.$data);

				return isValid;
			},

			onChange(value, name) {

				this[name] = value ? value : '';

				if (name === 'client_id') {

					if (value) { this.license_code = '' }
				}
			},

			generateCode() {
				this.license_code = generateRandomString(16);
			},

			onSubmit() {

				if (this.isValid()) {

					this.loading = true

					const data = {};

					if (this.license_id) {

						data['license_id'] = this.license_id;
					}

					data['api_key_secret'] = this.getApiKey;

					data['product_id'] = this.product_id ? this.product_id.id : '';

					data['license_status'] = this.license_status ? 1 : 0;

					data['license_require_domain'] = this.license_require_domain ? 1 : 0;

					if (this.license_order_number) { data['license_order_number'] = this.license_order_number; }

					data['license_ip'] = this.license_ip;

					data['license_domain'] = this.license_domain.toString();

					if (this.license_limit) { data['license_limit'] = this.license_limit; }

					data['license_comments'] = this.license_comments;

					if (this.license_expire_date) {
						data['license_expire_date'] = moment(this.license_expire_date).format("YYYY-MM-DD");
					}

					if (this.license_updates_date) {
						data['license_updates_date'] = moment(this.license_updates_date).format("YYYY-MM-DD");
					}

					if (this.license_support_date) {
						data['license_support_date'] = moment(this.license_support_date).format("YYYY-MM-DD");
					}

					if (!this.client_id) {

						data['license_code'] = this.license_code;
					}

					if (!this.license_code) {

						data['client_id'] = this.client_id ? this.client_id.id : '';
					}

					axios.post(this.apiEndpoint, data).then(res => {

						this.loading = false

						successHandler(res, 'license')

						if (!this.license_id) {

							setTimeout(() => {

								this.$router.push('/licenses')

							}, 2000)

						} else {

							this.getInitialValues(this.license_id)
						}

					}).catch(err => {

						this.loading = false

						errorHandler(err, 'license')
					});
				}
			}
		},

		components: {

			"text-field": require("components/Reusable/FormField/TextField").default,

			"number-field": require("components/Reusable/FormField/NumberField").default,

			"static-select": require("components/Reusable/FormField/StaticSelect").default,

			"dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,

			"radio-button": require("components/Reusable/FormField/RadioButton").default,
		}
	}
</script>
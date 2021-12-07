<template>
	
	<div class="col-sm-12">
		
		<div class="alert alert-info">
        	<p>Add new license to be used. It's possible to add licenses with and without client's profile.<br><br>With client's profile (when client's name and email address are known): select client and product from the list, and click the 'Submit'
            button. Client will need to enter his email address during script installation to verify his license.<br><br>Without client's profile (when anonymous license needs to be issued): select product from the list and enter unique license
            code (entering <b>random</b> will automatically generate a random code). Client will need to enter this code during script installation to verify his license.<br><br>If IP address and/or domain is set, product will only work on
            specified IP and/or domain. If licensed domain is entered as clientdomain.com, product will work on clientdomain.com and clientdomain.com/any/directory. If licensed domain is entered as clientdomain.com/path, product will only
            work on clientdomain.com/path. It's possible to add multiple licensed IPs and/or domains by separating them with comma (,) symbol. If installations limit is set, client will not be able to run more copies of licensed product than
            specified number.<br><br>If expiration date is set, application will stop working after this date (expiration date can be updated at any time).</p>
    	</div>
		
		<div class="row" v-if="!hasDataPopulated || loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="license"/>

		<div class="card card-light" v-if="hasDataPopulated">
			
			<div class="card-header">
				
				<h3 class="card-title">{{trans(title)}}</h3>

				&nbsp;<tool-tip :message="trans('license_tooltip')"></tool-tip>
			</div>

			<div class="card-body">
				
				<div class="row">

					<dynamic-select :label="trans('product')" :multiple="false" :elements="productOptions"
						name="product_id" classname="col-sm-6"
						:value="product_id" :onChange="onChange" :strlength="35"
						:required="true">
					</dynamic-select>

					<dynamic-select :label="trans('client')" :multiple="false" :elements="clientOptions"
						name="client_id" classname="col-sm-6"
						:value="client_id" :onChange="onChange" :strlength="35"
						:required="false">
					</dynamic-select>
				</div>

				<div class="row">
					
					<div class="col-md-6">

                        <div class="form-group">
                            
                            <label>{{trans('license_code')}} 

                            	<span v-if="!client_id">(<a href="javascript:;" @click="generateCode();return false;" class="text-primary">{{trans('generate')}}</a>)<span class="text-red"> *</span></span>

                            </label>
                            
                            <input type="text" name="license_code" v-model="license_code" class="form-control" placeholder="Code..."
                            	:disabled="client_id ? true : false">
                        </div>
                    </div>
				</div>
			</div>

			<div class="card-footer">
				
				<button class="btn btn-default" @click="onSubmit()"><i :class="iconClass"></i>&nbsp;&nbsp;{{trans(btnName)}}</button>
			</div>
		</div>
	</div>
</template>

<script>
	
	import axios from 'axios'

	import { successHandler, errorHandler } from 'helpers/responseHandler';

	import  { getIdFromUrl } from 'helpers/extraLogics';

	import { validateLicenseSettings } from "helpers/validator/licenseValidation.js";

	import { mapGetters } from 'vuex';

	export default {

		name : 'license-create-edit',

		data() {

			return {

				title : 'create_new_license',

				iconClass : 'fas fa-save',

				btnName : 'save',

				hasDataPopulated : false,

				loading : false,

				radioOptions:[{name:'active',value:1},{name:'inactive',value:0}],

				apiEndpoint : '',

				license_id : '',

				product_id : '',

				productOptions: [],

				client_id : '',

				license_code : '',

				clientOptions: [],
			}
		},

		beforeMount() {

			const path = window.location.pathname
			
			this.getValues(path);

			this.loadData();
		},

		computed : {

			...mapGetters(['getApiKey'])
		},	

		methods : {

			loadData() {
                
                this.loading = true;
                
                this.hasDataPopulated = false;

                Promise.all([this.getProducts(),this.getClients()]).then((values) => {
                    
                    [this.productOptions, this.clientOptions] = values;
                	
                	this.loading = false;

                	this.hasDataPopulated = true;

                }).catch(function (error) {

                    this.loading = false;

                	this.hasDataPopulated = true;
                });
            },

			getProducts() {

				axios.get('/api/admin/viewproducts').then(res=>{
					
					this.productOptions =  res.data.data.map(data=>{

						data.name = data.product_title;

						data.id = data.product_id;

						return data;
					})
				});

				return this.productOptions
			},

			getClients() {

				axios.get('/api/admin/viewClients').then(res=>{
					
					this.clientOptions = res.data.data.map(data=>{

						data.name = data.client_fname + ' ' + data.client_lname;

						data.id = data.client_id;

						return data;
					})
				});

				return this.clientOptions
			},

			getValues(path){

				const licenseId = getIdFromUrl(path)

				if(path.indexOf('edit') >= 0){

					this.title = 'edit_license'

					this.iconClass = 'fas fa-sync'

					this.btnName = 'update'

					this.hasDataPopulated = false

					this.getInitialValues(licenseId);

					this.license_id = licenseId;

					this.apiEndpoint = '/api/admin/licenses/edit';

				} else {

					this.loading = false;

					this.hasDataPopulated = true;

					this.apiEndpoint = '/api/admin/licenses/add';
				}
			},

			getInitialValues(id){

				this.loading = true
				
				axios.get('/api/admin/license/'+id).then(res=>{

					this.loading = false;

					this.hasDataPopulated = true

					this.updateStatesWithData(res.data.data.license);
				
				}).catch(error=>{
					
					this.loading = false;
				});
			},

			updateStatesWithData(data){

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
console.log(value,name)
				this[name] = value ? value : '';

				if(name === 'client_id') {

					if(value){ this.license_code = '' }
				}
			},

			generateCode() {

				var a = ''
                
                var n = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                
                for (var e = 1; e <= 16; e++) {
                
                    a += n.charAt(Math.floor(Math.random() * n.length));
                
                    if (e % 4 == 0 && e != 16) {
                
                        a += ''
                    }
                }

                this.license_code = a;
			},

			validLicense() {

				if(!this.client_id){

					if(!this.license_code){

						this.$store.dispatch('setAlert', { type: 'danger', message: `License Code is required.`, 
			    		component_name: 'license' });

					} else{
					
						return true
					}
				} else {

					return true
				}
			},

			onSubmit(){
			
				if(this.isValid() && this.validLicense()){

					this.loading = true 

					const data = {};

					if(this.license_id){

						data['license_id']= this.license_id;
					}

					data['api_key_secret']= this.getApiKey;
					
					data['product_id'] = this.product_id ? this.product_id.id : '';

					data['client_id'] = this.client_id ? this.client_id.id : '';

					if(!this.client_id){

						data['license_code'] = this.license_code;
					}
					
					axios.post(this.apiEndpoint, data).then(res => {

						this.loading = false
						
						successHandler(res,'license')
						
						if(!this.license_id){
							
							setTimeout(()=>{

								this.$router.push('/licenses')

							},2000)
							
						} else {

							this.getInitialValues(this.license_id)
						}

					}).catch(err => {
						
						this.loading = false
						
						errorHandler(err,'license')
					});
				}
			}
		},

		components : {

			"text-field": require("components/Reusable/FormField/TextField").default,

			"number-field": require("components/Reusable/FormField/NumberField").default,

			"static-select": require("components/Reusable/FormField/StaticSelect").default,

			"dynamic-select": require("components/Reusable/FormField/DynamicSelect").default,

			"radio-button": require("components/Reusable/FormField/RadioButton").default
		}
	}
</script>
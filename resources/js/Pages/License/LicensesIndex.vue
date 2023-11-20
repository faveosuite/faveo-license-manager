<template>

	<div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

		<alert componentName="dataTableModal" />

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

				<v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:product_title="props">

                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>
                    </template>

                    <template v-slot:license_status="props">

                        <span :class="props.row.license_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.license_status ? 'Active' : 'Inactive'}}
                        </span>
                    </template>

                    <template v-slot:actions="props">

                        <table-actions :data="props.row"></table-actions>
                    </template>
				</v-client-table>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';
  import {lang} from "../../helpers/extraLogics";

	export default {

		name: 'licenses-list',

		data() {

			return {
        loading: false,

        data: '',

				columns: ['product_title', 'license_code', 'total_installations', 'total_callbacks',
					'latest_callback', 'latest_license','actions'],

				options: {},

				counter: 0
			}
		},


		beforeMount() {

			const self = this;

			this.getData();

			this.options = {

				sortIcon: {

					base: 'glyphicon',

					up: 'glyphicon-chevron-up',

					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				columnsClasses: {

					product_title: 'license_product_title',

					license_code: 'license_code',

					total_installations: 'license_install',

					total_callbacks: 'license_callbacks',

					latest_callback: 'latest_callback',

                    latest_license: 'latest_license',

                    actions:      'actions',
				},

				templates: {
                    latest_license(h,row){
                        return row.latest_license ? row.latest_license : '---';
                    },

                    latest_callback(h,row){
                        return row.latest_callback ? row.latest_callback : '---';
                    },

                    license_code(h, row) {
                        const formattedLicenseCode = row.license_code ? row.license_code.match(/.{1,4}/g).join('-') : '----';
                        return formattedLicenseCode;
                    },

					license_date(h, row) {

						return row.license_date ? row.license_date : '---'
					},

					latest_callback_date_time(h, row) {

						return row.latest_callback_date_time ? row.latest_callback_date_time : '---';
					},

				},

				pagination: { chunk: 5, nav: 'fixed', edge: true },

				headings: {

					product_id: 'Product',

					license_code: 'License Code',

					total_installations: 'Installations',

					total_callbacks: 'Callbacks',

					latest_callback_date_time: 'Latest Callback',

					license_date: 'Latest License',

					actions: 'Actions'
				},
			}
		},

		methods: {
      lang: lang,
			updateData() {

				this.getData();
			},

			getData() {

                this.loading = true;

				axios.get('/api/admin/viewLicenses').then(res => {

                    this.loading = false;

					this.data = res.data.data.map(data => {

						data.edit_url = '/licenses/' + data.license_id + '/edit';

						data.delete_url = '/api/admin/license/delete';

						data.keyVal = 'license_id';

						data.idVal = data.license_id;

						return data;
					})
				}).catch(err => {

                    this.loading = false;
                })
			}
		}
	};
</script>

<style>
	.license_product_title,
	.license_code,
	.license_install,
	.license_callbacks,
	.latest_callback_time,
	.license_date {
		max-width: 200px;
		word-break: break-all;
	}

	#my_licenses .VueTables .table-responsive {
		overflow-x: auto;
		overflow-y: hidden;
	}

	#my_licenses .VueTables .table-responsive>table {
		width: max-content;
		min-width: 100%;
		max-width: max-content;
		overflow: auto !important;
	}
</style>

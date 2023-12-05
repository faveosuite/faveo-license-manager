<template>

	<div class="col-sm-12">

		<div class="row" v-if="loading">

			<custom-loader :duration="4000"></custom-loader>
		</div>

		<alert componentName="dataTableModal" />

		<div class="card card-light ">

			<div class="card-header">

				<h3 class="card-title">{{lang('installations')}}</h3>
			</div>

			<div class="card-body" id="my_installations">

				<v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

                    <template v-slot:product_title="props">

                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>
                    </template>

                    <template v-slot:installation_status="props">

                        <span :style="{ color: props.row.installation_status ? 'green' : 'red' }">

                            {{ props.row.installation_status ? 'Active' : 'Inactive'}}
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

	export default {

		name: 'installations-list',

		data() {

			return {

				data: '',

				columns: ['product_title', 'license_code', 'total_installations', 'latest_installation_date', 'installation_status', 'actions'],

				options: {},

				counter: 0,

				loading: false
			}
		},

		created() {

			this.emitter.on('refreshData', this.updateData);
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

					product_title: 'i_product_title',

					license_code: 'i_license_code',

					total_installations: 'i_total_installations',

					latest_installation_date: 'i_latest_installation',

					installation_status: 'i_installation_status',
				},

				templates: {

                    license_code(h, row) {
                        const formattedLicenseCode = row.license_code ? row.license_code.match(/.{1,4}/g).join('-') : '----';
                        return formattedLicenseCode;
                    },

					latest_installation_date(h, row) {

						return row.latest_installation_date ? row.latest_installation_date : '---';
					}
				},

				pagination: { chunk: 5, nav: 'fixed', edge: true },

				headings: {

					product_title: 'Product',

					license_code: 'License Code',

					total_installations: 'Total Installations',

					latest_installation_date: 'Latest Installation',

					installation_status: 'Status',

					actions: 'Actions'
				},
			}
		},

		methods: {

			updateData() {

				this.getData();
			},

			getData() {

				this.loading = true;

				axios.get('/api/admin/viewInstallations').then(res => {

					this.loading = false;

					this.data = res.data.data.map(data => {

						data.edit_url = '/installations/' + data.installation_id + '/edit';

						data.delete_url = '/api/admin/installations/delete';

						data.keyVal = 'installation_id';

						data.idVal = data.installation_id;

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
	.i_product_title,
	.i_license_code,
	.i_total_installations,
	.i_latest_installation,
	.i_installation_status {
		max-width: 200px;
		word-break: break-all;
	}

	#my_installations .VueTables .table-responsive {
		overflow-x: auto;
		overflow-y: hidden;
	}

	#my_installations .VueTables .table-responsive>table {
		width: max-content;
		min-width: 100%;
		max-width: max-content;
		overflow: auto !important;
	}
</style>

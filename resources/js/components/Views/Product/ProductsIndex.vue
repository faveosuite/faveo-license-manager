<template>

	<div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

		<alert componentName="dataTableModal" />

		<div class="card card-light ">

			<div class="card-header">

				<h3 class="card-title">{{lang('products')}}</h3>

				<div class="card-tools">

					<router-link to="/products/create" class="btn-tool" v-tooltip="lang('create_product')">

						<i class="fas fa-plus"></i>
					</router-link>
				</div>
			</div>

			<div class="card-body" id="my_products">

				<v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

				</v-client-table>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';

	export default {

		name: 'products-list',

		data() {

			return {

				data: '',

				columns: ['product_title', 'product_sku', 'product_url_homepage', 'product_version', 'total_licenses', 'total_installations', 'product_status', 'actions'],

				options: {},

				counter: 0
			}
		},

		created() {

			window.eventHub.$on('refreshData', this.updateData);
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

					product_title: 'product_title',

					product_sku: 'product_sku',

					product_url_homepage: 'product_url',

					product_status: 'product_status',

					product_version: 'product_version',

					total_licenses: 'product_licenses',

					total_installations: 'product_installations'
				},

				templates: {

					product_title(createElement, row) {

						return createElement('router-link', {
							attrs: {
								to: '/products/' + row.product_id + '/edit'
							}
						}, row.product_title);
					},

					product_url_homepage(createElement, row) {

						return createElement('a', {
							attrs: {
								href: row.product_url_homepage,
								target: '_blank'
							}
						}, row.product_url_homepage);
					},

					product_version(h, row) {

						return row.product_version ? row.product_version : '---'
					},

					product_status(createElement, row) {

						let span = createElement('span', {

							attrs: {
								'class': row.product_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.product_status ? 'Active' : 'Inactive');

						return createElement('a', {}, [span]);
					},

					actions: 'table-actions'
				},

				pagination: { chunk: 5, nav: 'fixed', edge: true },

				headings: {

					product_title: 'Product',

					product_sku: 'SKU',

					product_url_homepage: 'Homepage',

					product_version: 'Version',

					total_licenses: 'Licenses',

					total_installations: 'Installations',

					product_status: 'Status',

					actions: 'Actions'
				},
			}
		},

		methods: {

			updateData() {

				this.counter++;

				this.getData();
			},

			getData() {

                this.loading = true;

				axios.get('/api/admin/viewproducts').then(res => {

                    this.loading = false;

					this.data = res.data.data.map(data => {

						data.edit_url = '/products/' + data.product_id + '/edit';

						data.delete_url = '/api/admin/products/delete';

						data.keyVal = 'product_id';

						data.idVal = data.product_id;

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
	.product_title,
	.product_sku,
	.product_url,
	.product_status,
	.product_version,
	.product_licenses .product_installations {
		max-width: 200px;
		word-break: break-all;
	}

	#my_products .VueTables .table-responsive {
		overflow-x: auto;
		overflow-y: hidden;
	}

	#my_products .VueTables .table-responsive>table {
		width: max-content;
		min-width: 100%;
		max-width: max-content;
		overflow: auto !important;
	}
</style>

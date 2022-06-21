<template>

	<div class="col-sm-12">

        <div class="row" v-if="loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

		<alert componentName="dataTableModal" />

		<div class="card card-light ">

			<div class="card-header">

				<h3 class="card-title">{{lang('Clients')}}</h3>

				<div class="card-tools">

					<router-link to="/clients/create" class="btn-tool" v-tooltip="lang('create_client')">

						<i class="fas fa-plus"></i>
					</router-link>
				</div>
			</div>

			<div class="card-body" id="my_clients">

				<v-client-table v-if="data" :columns="columns" v-model="data" :options="options" :key="counter">

				</v-client-table>
			</div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';

	export default {

		name: 'clients-list',

		data() {

			return {

				data: '',

				columns: ['full_name', 'client_email', 'client_active_date', 'client_status', 'actions'],

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

					full_name: 'client_fname' + 'client_fname',

					client_email: 'client_email',

					client_active_date: 'client_date',

					client_status: 'client_status'
				},

				templates: {

					client_status(createElement, row) {

						let span = createElement('span', {

							attrs: {
								'class': row.client_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'
							}
						}, row.client_status ? 'Active' : 'Inactive');

						return createElement('a', {}, [span]);
					},

					full_name: function (createElement, row) {

						return createElement('router-link', {

							attrs: {
								to: '/clients/' + row.client_id + '/edit',
							}

						}, row.client_fname + ' ' + row.client_lname);
					},

					actions: 'table-actions'
				},

				pagination: { chunk: 5, nav: 'fixed', edge: true },

				headings: {

					full_name: 'Full Name',

					client_email: 'Email',

					client_active_date: 'Active Date',

					client_status: 'Status',

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

				axios.get('/api/admin/viewClients').then(res => {

                    this.loading = false;

					this.data = res.data.data.map(data => {

						data.edit_url = '/clients/' + data.client_id + '/edit';

						data.delete_url = '/api/admin/clients/delete';

						data.keyVal = 'client_id';

						data.idVal = data.client_id;

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
	.client_name,
	.client_email,
	.client_date,
	.client_status {
		max-width: 200px;
		word-break: break-all;
	}

	#my_clients .VueTables .table-responsive {
		overflow-x: auto;
		overflow-y: hidden;
	}

	#my_clients .VueTables .table-responsive>table {
		width: max-content;
		min-width: 100%;
		max-width: max-content;
		overflow: auto !important;
	}
</style>

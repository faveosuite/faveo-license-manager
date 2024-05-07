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

				<v-client-table v-on:limit="onPerPageChange" v-if="data" :columns="columns" :data="data" :options="options" :key="counter">

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


                <div class="pagination-container mt-2">

                    <div v-if="!loading">
                        <div v-if="total == 1">
                            {{trans('one_record')}}
                        </div>
                        <div v-if="total > 1 && total <= 10">
                            {{ total }} {{trans('records')}}
                        </div>
                        <div v-if="total > 10">
                            {{trans('showing')}} {{ from }} to {{ to }} of {{ total }} {{trans('records')}}
                        </div>
                    </div>

                    <div v-if="!loading" class="float-right mr-0 pt-2">

                        <simple-paginaton :prev_page="prev_page" :next_page="next_page" :onPagination="onPagination"></simple-paginaton>
                    </div>

                </div>

            </div>
		</div>
	</div>
</template>

<script>

    import axios from 'axios';
  import {lang} from "../../helpers/extraLogics";
  import SimplePagination from "../../components/Reusable/FormField/SimplePagination.vue";

	export default {

		name: 'licenses-list',

		data() {

			return {

                loading: false,

                data: '',

                columns: ['product_title', 'license_code', 'installations_count', 'callbacks_count',
                    'latest_callback_date', 'latest_license_date','actions'],

                options: {},

                prev_page : '',

                next_page : '',

                per_page : 100,

                endPoint : `/api/admin/viewLicenses?page=1&perPage=100`,

				counter: 0,

                total : '',

                from : '',

                to : ''

			}
		},


		beforeMount() {

			const self = this;

			this.getData();

			this.options = {

                perPage : 10,

                // perPageValues : [10, 20, 45, 50, 100],

                pagination: { dropdown : false, show : false },

				sortIcon: {

					base: 'glyphicon',

					up: 'glyphicon-chevron-up',

					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

				columnsClasses: {

					product_title: 'license_product_title',

					license_code: 'license_code',

					installations_count: 'license_install',

					callbacks_count: 'license_callbacks',

					latest_callback_date: 'latest_callback_date',

                    latest_license_date: 'latest_license',

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

					latest_license_date(h, row) {

						return row.latest_license_date ? row.latest_license_date : '---'
					},

					latest_callback_date(h, row) {

						return row.latest_callback_date ? row.latest_callback_date : '---';
					},

				},

				headings: {

					product_id: 'Product',

					license_code: 'License Code',

					installations_count: 'Installations',

					callbacks_count: 'Callbacks',

					latest_callback_date: 'Latest Callback',

					latest_license_date: 'Latest License',

					actions: 'Actions'
				},
			}
		},

		methods: {

            lang: lang,

            onPerPageChange(payload) {

                this.per_page = payload;

                this.endPoint = `/api/admin/viewLicenses?page=1&perPage=${payload}`;

                this.getData();
            },

			updateData() {

				this.getData();
			},

			getData() {

                this.loading = true;

				axios.get(this.endPoint).then(res => {

                    this.loading = false;

                    this.next_page = res.data.data.next_page_url;

                    this.prev_page = res.data.data.prev_page_url;

                    this.total = res.data.data.total;

                    this.from = res.data.data.from;

                    this.to = res.data.data.to;

					this.data = res.data.data.data.map(data => {

						data.edit_url = '/licenses/' + data.license_id + '/edit';

						data.delete_url = '/api/admin/license/delete';

						data.keyVal = 'license_id';

						data.idVal = data.license_id;

						return data;
					})
				}).catch(err => {

                    this.loading = false;
                })
			},

            onPagination(direction) {

                const targetUrl = direction === 'next' ? this.next_page : this.prev_page;

                if (targetUrl) {

                    const url = new URL(targetUrl);

                    const pageValue = url.searchParams.get("page");

                    this.endPoint = this.updateQueryParam(this.endPoint, "page", pageValue);

                    this.getData();
                }
            },

            updateQueryParam(url, param, value) {

                url = url.replace(/([?&])page=\d+/, '');

                url = url.replace(/([?&])perPage=\d+/, '');

                const separator = url.includes('?') ? '&' : '?';

                return `${url}${separator}${param}=${value}&perPage=${this.per_page}`;
            }
		},

        components : {

            'simple-paginaton' : SimplePagination
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

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
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

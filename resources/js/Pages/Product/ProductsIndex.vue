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

                <data-table :url="endPoint" :show_pagination="true" alertComponentName="dataTableModal" :dataColumns="columns" :option="options" scroll_to="products-list">

                </data-table>

            </div>
		</div>
	</div>
</template>

<script>

    import {lang} from "../../helpers/extraLogics";
    import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";

	export default {

		name: 'products-list',

        methods : {
            lang
        },

		data() {

			return {

                loading: false,

                data: '',

				columns: ['product_title', 'product_sku', 'product_url_homepage', 'product_version', 'licenses_count', 'installations_count', 'product_status', 'actions'],

				options: {},

				counter: 0,

                endPoint : '/api/admin/viewproducts?page=1'
			}
		},

		beforeMount() {

			const self = this;

			this.options = {

				sortIcon: {

					base: 'glyphicon',

					up: 'glyphicon-chevron-up',

					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

                sortable:  ['product_title', 'product_sku', 'product_url_homepage', 'product_version', 'licenses_count', 'installations_count', 'product_status'],

                filterable : [ 'product_title' ],

                requestAdapter(data) {

                    return {

                        'sort_field' : data.orderBy ? data.orderBy : 'product_id',

                        'sort_order' : data.ascending ? 'desc' : 'asc',

                        'search_query' : data.query,

                         perPage : data.limit,
                    }
                },

                responseAdapter({data}) {

                    return {

                        data: data.data.data.map(data => {

                            data.edit_url = '/products/' + data.product_id + '/edit';

                            data.delete_url = '/api/admin/products/delete';

                            data.keyVal = 'product_id';

                            data.idVal = data.product_id;

                            return data;
                        }),

                        count: data.data.total
                    }
                },

				columnsClasses: {

					product_title: 'product_title',

					product_sku: 'product_sku',

					product_url_homepage: 'product_url',

					product_status: 'product_status',

					product_version: 'product_version',

                    licenses_count: 'product_licenses',

                    installations_count: 'product_installations'
				},

				templates: {

                    product_version(h, row) {

                        return row.product_version ? row.product_version : '---'
                    },

                    product_sku(h, row) {

                        return row.product_sku ? row.product_sku : '---'
                    },

                    product_url_homepage(h, row) {

                        return row.product_url_homepage ? row.product_url_homepage : '---'
                    },

                    total_licenses(h, row) {

                        return row.total_licenses ? row.total_licenses : '---'
                    },

                    installations_count(h, row) {

                        return row.total_installations ? row.total_installations : '---'
                    },
				},

				pagination: { show : false },

				headings: {

					product_title: 'Product',

					product_sku: 'SKU',

					product_url_homepage: 'Homepage',

					product_version: 'Version',

					licenses_count: 'Licenses',

					total_installations: 'Installations',

					product_status: 'Status',

					actions: 'Actions'
				},
			}
		},

        components : {

            'data-table' : DynamicDataTable
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

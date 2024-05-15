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

<!--				<v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">-->

<!--                    <template v-slot:product_title="props">-->

<!--                        <router-link :to="'/products/' + props.row.product_id + '/edit'">{{props.row.product_title}}</router-link>-->
<!--                    </template>-->

<!--                    <template v-slot:product_url_homepage="props">-->

<!--                        <a v-if="props.row.product_url_homepage" :href="props.row.product_url_homepage" target="_blank">{{props.row.product_url_homepage}}</a>-->

<!--                        <span v-else>&#45;&#45;</span>-->
<!--                    </template>-->

<!--                    <template v-slot:product_status="props">-->

<!--                        <span :style="{ color: props.row.product_status ? 'green' : 'red' }">-->

<!--                            {{ props.row.product_status ? 'Active' : 'Inactive'}}-->
<!--                        </span>-->
<!--                    </template>-->
<!--				</v-client-table>-->

                <data-table :url="endPoint" :show_pagination="true" :dataColumns="columns" :option="options" scroll_to="products-list">

                </data-table>

            </div>
		</div>
	</div>
</template>

<script>

	import axios from 'axios';

  import {lang} from "../../helpers/extraLogics";
    import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
    import {useStore} from "vuex";
    import {computed} from "vue";

	export default {

		name: 'products-list',

        // setup() {
        //
        //     const store = useStore();
        //
        //     return {
        //
        //         formattedTime : computed(()=>store.getters.formattedTime)
        //     }
        // },

		data() {

			return {

                loading: false,

                data: '',

				columns: ['product_title', 'product_sku', 'product_url_homepage', 'product_version', 'total_licenses', 'total_installations', 'product_status', 'actions'],

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

                sortable:  ['product_title', 'product_sku', 'product_url_homepage', 'product_version', 'total_licenses', 'total_installations'],

                filterable : [ 'product_title' ],

                requestAdapter(data) {
                    console.log('request', data)

                    return {

                        'sort_field' : data.orderBy ? data.orderBy : '',

                        'sort_order' : data.ascending ? 'desc' : 'asc',

                        'search_query' : data.query,

                        // page : data.page,

                        perPage : data.limit,
                    }
                },

                responseAdapter({data}) {
                    console.log('response',data);
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

					total_licenses: 'product_licenses',

					total_installations: 'product_installations'
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

                    total_installations(h, row) {

                        return row.total_installations ? row.total_installations : '---'
                    },
				},

				pagination: { show : false },

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

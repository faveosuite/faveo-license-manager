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

                <data-table :url="endPoint" :show_pagination="true" alertComponentName="dataTableModal" :dataColumns="columns" :option="options" scroll_to="licenses-list">

                </data-table>

            </div>
		</div>
	</div>
</template>

<script>

import {formatDateTime, lang} from '../../helpers/extraLogics'
    import DynamicDataTable from "../../components/Reusable/DynamicDataTable.vue";
    import {useStore} from "vuex";
    import {computed} from "vue";

	export default {

		name: 'installations-list',

        methods : {
            lang
        },

        setup() {

            const store = useStore();

            return {

                formattedTime : computed(()=>store.getters.formattedTime)
            }
        },

        props : {
            generalSetting : {type : Object, default : () => {}},
        },

		data() {

			return {

				data: '',

				columns: ['product_title', 'license_code', 'total_installations', 'latest_installation_date', 'installation_status', 'actions'],

				options: {},

				counter: 0,

				loading: false,

                endPoint : '/api/admin/viewInstallations?page=1',
			}
		},

		beforeMount() {

			const self = this;

            const date_format = this.generalSetting.date_format.js_format
            const time_format = this.generalSetting.time_format.js_format
            const timezone = this.generalSetting.timezone.name

			this.options = {

				sortIcon: {

					base: 'glyphicon',

					up: 'glyphicon-chevron-up',

					down: 'glyphicon-chevron-down'
				},

				texts: { filter: '', limit: '' },

                sortable:  ['product_title', 'license_code', 'total_installations', 'latest_installation_date', 'total_installations', 'installation_status'],

                filterable : [ 'product_title' ],

                requestAdapter(data) {

                    return {

                        'sort_field' : data.orderBy ? data.orderBy : 'installation_id',

                        'sort_order' : data.ascending ? 'desc' : 'asc',

                        'search_query' : data.query,

                         perPage : data.limit,
                    }
                },

                responseAdapter({data}) {

                    return {

                        data: data.data.data.map(data => {

                            data.edit_url = '/installations/' + data.installation_id + '/edit';

                            data.delete_url = '/api/admin/installations/delete';

                            data.keyVal = 'installation_id';

                            data.idVal = data.installation_id;

                            return data;
                        }),
                        count: data.data.total
                    }
                },

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

                        return formatDateTime(row.latest_installation_date, timezone, date_format, time_format)
                    },
				},

				pagination: { show : false },

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

        components : {

            'data-table' : DynamicDataTable
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

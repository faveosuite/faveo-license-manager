<template>

    <div class="col-md-12 col-sm-12 col-12">
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">{{ 'Latest Products' }}</h3>
            </div>
            <div class="card-body" id="afl_products">
                <div class="datatable-container">
                    <v-client-table
                        v-if="data"
                        :columns="columns"
                        :data="data"
                        :options="options"
                        :key="counter"
                    >
                        <template v-slot:product_status="props">

                        <span :class="props.row.product_status ? 'btn btn-success btn-xs' : 'btn btn-danger btn-xs'">

                            {{ props.row.product_status ? 'Active' : 'Inactive'}}
                        </span>
                        </template>
                    </v-client-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import {errorHandler} from "../../helpers/responseHandler";

export default {
    name :'latest-product',

    data(){

        return {

            data: [],

            columns:['product_title','product_sku','product_date','product_status'],

            options : {},

            counter: 0,
        };
    },

    beforeMount(){

        const self =this;

        this.getData();

        this.options ={

            columnsClasses:{

                product_title: 'product_title',

                product_sku:   'product_sku',

                product_date: 'product_date',

                product_status: 'product_status',
            },

            templates: {

                product_title(h,row){
                    return row.product_title ?row.product_title : '----';
                },

            },

            headings: {

                product_title: 'Product',

                product_sku: 'SKU',

                product_date: 'Date',

                product_status: 'Status'
            },
        }
    },

    methods:{

        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.data = res.data.data.latest_products; // Assign the fetched data to the data property
                    this.loading = false;
                })
                .catch((err) => {
                    this.loading = false

                    errorHandler(err, 'latest-products')
                });
        },
    },
};

</script>
<style>

#afl_products .VueTables__search-field{
    display : none;
}

#afl_products .VuePagination .text-center {
    display : none;
}
#afl_products .datatable-container {
    max-height: 300px; /* Adjust the maximum height as per your needs */
    overflow-y: auto;
    overflow-x: scroll; /* Allow horizontal scrolling */
    scrollbar-width: thin; /* Width of the scrollbar */
    scrollbar-color: transparent transparent;
}
#afl_products  .glyphicon-sort {
    margin-left: 100px;
    visibility: hidden;
    margin-top: -19px;
}
#afl_products .VueTables .table-responsive {
    display: block;
    width: 100%;
    position: inherit;
    overflow-x: visible;
}
/* Style the scrollbar track and thumb */
.datatable-container::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.datatable-container::-webkit-scrollbar-thumb {
    background-color: transparent;
}
.datatable-container::-webkit-scrollbar-track {
    background-color: transparent;
}
</style>

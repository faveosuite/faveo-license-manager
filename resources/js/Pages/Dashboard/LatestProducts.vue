<template>

  <div class="col-md-12 col-sm-12 col-12">
      <div class="card card-light">
          <div class="card-header">
              <h3 class="card-title">{{ 'Latest Products' }}</h3>
             </div>
              <div class="card-body" id="afl_products">

                  <v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter"></v-client-table>
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

             columns:['product_id','product_title','product_description'],

             options : {},

             counter: 0,
         };
    },

    beforeMount(){

         const self =this;

         this.getData();

         this.options ={

             columnsClasses:{

                 product_id: 'product_id',

                 product_title: 'product_title',

                 product_description: 'product_description',
             },

             templates: {

                 product_id(h,row){
                     return row.product_id ?row.product_id : '----';
                 },

                 product_title(h,row){
                     return row.product_title ?row.product_title : '----';

                 },

                 product_description(h,row){
                     return row.product_description ?row.product_description : '----';

                 },

             },

             headings: {

                 product_id: 'Product',

                 product_title: 'Title',

                 product_description: 'Description',

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

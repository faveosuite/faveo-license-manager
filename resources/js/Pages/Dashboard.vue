<template>

    <div class="col-sm-12">

        <h5 class="mb-2">Info Box</h5>


        <div class="row">

            <div class="col-md-3 col-sm-6 col-12" v-for="(item, index) in items" :key="index">
                <div class="info-box shadow-none">

                    <span class="info-box-icon bg-info"><i class="fa fa-address-card" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ subString(item.key, item.icon_class ? 20 : 40) }}</span>
                        <span class="info-box-number">{{ item.value }}</span>
                        <span class="info-box-text">Products</span>
                        <span class="info-box-number"></span>

                    </div>
                </div>
            </div>


            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-sm">

                    <span class="info-box-icon bg-success"><i class="fa fa-check" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Version</span>

                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow">

                    <span class="info-box-icon bg-warning"><i class="fa fa-user-circle" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Clients</span>

                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-lg">

                    <span class="info-box-icon bg-danger"><i class="fa fa-cog" aria-hidden="true"></i></span>

                    <div class="info-box-content">


                        <span class="info-box-text">Licenses</span>

                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-lg">

                    <span class="info-box-icon bg-danger"><i class="fa fa-phone" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Callbacks</span>

                        <span class="info-box-number"></span>
                    </div>
                </div>
            </div>


        </div>

    </div>

</template>

<script>

import axios  from 'axios';
import {errorHandler} from "../helpers/responseHandler";
import {item} from "../../../public/js/app";

export default {
    name: 'dashboard',
    data() {
        return {
            hasDataFetched: false,
            items: [], // Array to hold the values from the API response
        };
    },


    beforeMount() {
        this.getData();
    },

    methods: {
        item() {
            return item;
        },
        getData() {
            axios.get('/api/admin/dashboarddropdown')
                .then(res => {
                    this.items = res.data;
                })
                .catch(err => {
                    errorHandler(err, 'dashboard');
                })
                .finally(() => {
                    this.hasDataFetched = true;
                });
        },

    }
}
</script>


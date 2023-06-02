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

                        <span class="info-box-text">Products</span>

                        <span class="info-box-number">{{ product }}</span>

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-sm">

                    <span class="info-box-icon bg-success"><i class="fa fa-check" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Version</span>

                        <span class="info-box-number">{{ version }}</span>

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow">

                    <span class="info-box-icon bg-warning"><i class="fa fa-user-circle" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Clients</span>

                        <span class="info-box-number">{{ clients }}</span>

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-lg">

                    <span class="info-box-icon bg-danger"><i class="fa fa-cog" aria-hidden="true"></i></span>

                    <div class="info-box-content">


                        <span class="info-box-text">Licenses</span>

                        <span class="info-box-number">{{ licenses }}</span>

                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">

                <div class="info-box shadow-lg">

                    <span class="info-box-icon bg-danger"><i class="fa fa-phone" aria-hidden="true"></i></span>

                    <div class="info-box-content">

                        <span class="info-box-text">Callbacks</span>

                        <span class="info-box-number">{{ callbacks }}</span>

                    </div>
                </div>
            </div>


        </div>
    </div>
        <div class="col-md-12 col-12">
            <div class="row justify-content-around">
                <div class="info-box shadow-none body-scrollable col-md-6 justify-content-around">
                    <latest-product></latest-product>
                </div>
                <div class="info-box shadow-none col-md-6 justify-content-around">
                    <latest-version></latest-version>
                </div>
            </div>

            <div class="row">
                <div class="info-box shadow-none col-md-6">
                    <latest-installations></latest-installations>
                </div>
                <div class="info-box shadow-none col-md-6">
                    <latest-callbacks></latest-callbacks>
                </div>
            </div>

            <div class="row">
                <div class="info-box shadow-none col-md-6">
                    <latest-product-report></latest-product-report>
                </div>
                <div class="info-box shadow-none col-md-6">
                    <expiring-version></expiring-version>
                </div>
            </div>
        </div>
</template>

<script>
import axios from 'axios';
import { errorHandler } from '../helpers/responseHandler';
import LatestProduct from "./Dashboard/LatestProducts.vue";
import LatestVersion from "./Dashboard/LatestVersions.vue";
import LatestInstallations from "./Dashboard/LatestInstallations.vue";
import LatestCallbacks from "./Dashboard/LatestCallbacks.vue";
import LatestProductReport from "./Dashboard/LatestProductReport.vue";
import ExpiringVersion from "./Dashboard/ExpiringVersion.vue";

export default {
    name: 'dashboard',
    components: {
        ExpiringVersion,
        LatestProductReport,
        LatestCallbacks,
        LatestInstallations,
        LatestVersion,
        LatestProduct

    },
    data() {
        return {
            version: '', // Initialize with a default value
            clients: '',
            product:'',
            licenses: '',
            callbacks: '',
            hasDataFetched: false,
            items: [], // Array to hold the values from the API response
            widgetKeys: [],
        };
    },

    beforeMount() {
        this.getData();
    },

    methods: {
        getData() {
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    const { data } = res.data;

                    // console.log('test',res.data.products_count)
                    if (data) {
                        // alert(data)

                        // this.version = data.data.version; // Check if data exists before assigning to the variable
                        this.product= data.products_count;
                        this.installations =data.data.installlation_count;
                        this.clients = data.data.clients;
                        this.version =data.data.version_count
                        this.licenses = data.data.licenses;
                        this.callbacks = data.data.callback_count;
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    },
};
</script>

<style>
.VueTables__search-field{
    display : none;
}
.VuePagination{
    display : none;
}
.VueTables__limit{
    display : none;
}
</style>


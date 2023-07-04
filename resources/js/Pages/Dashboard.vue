<template>
<div class="container">
    <div class="col-sm-12" >

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


            <div class="col-lg-3 col-xs-6" style ="margin:-6px">
                <div class="small-box bg-light-blue">
                    <div class="inner" ><h3 class="word_wrap" style="font-size: 1.8rem;">Products: {{products}}</h3>
                    </div>
                    <div class="icon"><i class="fas fa-cart-arrow-down"></i>
                    </div>
                    <router-link class="small-box-footer"  to="/products/list">
                        ViewAll
                        <i class="far fa-arrow-alt-circle-right"></i>
                    </router-link>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6" style ="margin:-6px">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3 class="word_wrap" style="font-size: 1.8rem;">Versions: {{versions}}</h3>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i>
                    </div>
                    <router-link class="small-box-footer"  to="/versions/list">
                        ViewAll
                        <i class="far fa-arrow-alt-circle-right"></i>
                    </router-link>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow" style ="margin:-6px">
                    <div class="inner"><h3 class="word_wrap" style="font-size: 1.8rem;">licenses: {{installations}}</h3>
                    </div>
                    <div class="icon"><i class="fas fa-id-card" ></i>
                    </div>
                    <router-link class="small-box-footer"  to="/licenses/list">
                        ViewAll
                        <i class="far fa-arrow-alt-circle-right"></i>
                    </router-link>
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

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red" style ="margin:-6px">
                    <div class="inner"><h3 class="word_wrap" style="font-size: 1.8rem;">Callbacks: {{callbacks}}</h3>
                    </div>
                    <div class="icon"><i class="fas fa-phone"></i>
                    </div>
                    <router-link class="small-box-footer"  to="/callbacks/list">
                        ViewAll
                        <i class="far fa-arrow-alt-circle-right"></i>
                    </router-link>
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
            versions: '', // Initialize with a default value
            clients: '',
            products:'',
            licenses: '',
            callbacks: '',
            items: [], // Array to hold the values from the API response
            widgetKeys: [],
        };
    },

    beforeMount() {
        this.getData();
    },

    methods: {
        getData() {
            this.loading = true;
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.loading = false;
                    const { data } = res.data;

                    // console.log('test',res.data.products_count)
                    if (data) {
                        // alert(data)

                        // this.version = data.data.version; // Check if data exists before assigning to the variable
                        this.products= data.products_count;
                        this.installations =data.installlation_count;
                        this.versions =data.version_count
                        this.callbacks = data.callback_count;



                    }
                })
                .catch((error) => {
                    this.loading = false;
                });
        },
    },
};
</script>

<style>

.word_wrap{
    font-size: 1.9rem;
}</style>


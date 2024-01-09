<template>
    <div class="row" v-if="loading">

        <custom-loader :duration="4000"></custom-loader>
    </div>


            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{products}}</h3>
                                <p>{{lang('products')}}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cart-arrow-down"></i>
                            </div>
                            <router-link class="small-box-footer"  to="/products/list">
                                {{lang('view_all')}}
                                <i class="far fa-arrow-alt-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3> {{versions}}<sup style="font-size: 20px"></sup></h3>
                                <p>{{lang('versions')}}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <router-link class="small-box-footer disabled-link" to="/versions/list">
                                {{lang('view_all')}}
                                <i class="far fa-arrow-alt-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3> {{installations}}</h3>
                                <p>{{lang('licenses')}}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-id-card" ></i>
                            </div>
                            <router-link class="small-box-footer"  to="/licenses/list">
                                {{lang('view_all')}}
                                <i class="far fa-arrow-alt-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{callbacks}}</h3>
                                <p>{{lang('callbacks')}}</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <router-link class="small-box-footer"  to="/callbacks/list">
                                {{lang('view_all')}}
                                <i class="far fa-arrow-alt-circle-right"></i>
                            </router-link>
                        </div>
                    </div>

                </div>
            </div>


            <div class="row justify-content-around">
                <div class="shadow-none body-scrollable col-md-6 justify-content-around">
                    <latest-product></latest-product>
                </div>
                <div class="shadow-none col-md-6 justify-content-around">
                    <latest-version></latest-version>
                </div>
            </div>

            <div class="row">
                <div class=" shadow-none col-md-6">
                    <latest-installations></latest-installations>
                </div>
                <div class="shadow-none col-md-6">
                    <latest-callbacks></latest-callbacks>
                </div>
            </div>

            <div class="row">
                <div class="shadow-none col-md-6">
                    <latest-product-report></latest-product-report>
                </div>
                <div class="shadow-none col-md-6">
                    <expiring-version></expiring-version>
                </div>
            </div>


</template>
<script>
import axios from 'axios';
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
            versions: '',
            clients: '',
            products: '',
            licenses: '',
            callbacks: '',
            items: [],
            widgetKeys: [],
            loading: true, // Add loading state
        };
    },

    mounted() {
        this.getData();
    },

    methods: {
        getData() {
            this.loading = true; // Set loading state to true before making the request
            axios
                .get('/api/admin/dashboarddropdown')
                .then((res) => {
                    this.loading = false; // Set loading state to false after the request is completed
                    const { data } = res.data;

                    if (data) {
                        this.products = data.products_count;
                        this.installations = data.installlation_count;
                        this.versions = data.version_count;
                        this.callbacks = data.callback_count;
                    }
                })
                .catch((error) => {
                    this.loading = false; // Set loading state to false if an error occurs
                });

            axios.get('/api/admin/viewApiKeys').then(res => {

                this.$store.dispatch('setApiKey',res.data.data[0].api_key_secret);

            }).catch(err => {

                this.$store.dispatch('setApiKey');

            });
        },
    },
};
</script>

<style>
.disabled-link {
    pointer-events: none;
    cursor: default;
    color: #999999;
    text-decoration: none;
    opacity: 0.6;
}

.word_wrap{
    font-size: 1.9rem;
}
</style>

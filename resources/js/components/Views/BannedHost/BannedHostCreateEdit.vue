<template>

    <div class="col-sm-12">

        <div class="alert alert-info">
            <p>Add new banned host to be blocked from using Auto PHP Licenser. Enter IP address and click the 'Submit'
                button.</p>
        </div>

        <div class="row" v-if="!hasDataPopulated || loading">

            <custom-loader :duration="4000"></custom-loader>
        </div>

        <alert componentName="banned-hosts" />

        <div class="card card-light" v-if="hasDataPopulated">

            <div class="card-header">

                <h3 class="card-title">{{lang(title)}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('ip_address')" :value="ipAddress" :onChange="onChange" name="ip_address"
                        type="text" classname="col-sm-6">

                    </text-field>

                    <text-field :label="lang('comments')" type="text" :value="comments" :onChange="onChange"
                        name="comments" classname="col-sm-6">

                    </text-field>
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-default" @click="onSubmit"><i
                        :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
</template>

<script>

    import axios from 'axios'

    import { successHandler, errorHandler } from 'helpers/responseHandler';

    import { getIdFromUrl } from 'helpers/extraLogics';

    import { mapGetters } from 'vuex';

    import moment from 'moment'

    export default {

        name: 'Add-New-Banned-Host',

        data() {

            return {

                title: 'add_new_banned_host',

                iconClass: 'fas fa-save',

                btnName: 'save',

                hasDataPopulated: false,

                loading: false,

                apiEndpoint: '',

                moment: moment,

                ipAddress: null,

                comments: null,

                hostId: null
            }
        },

        beforeMount() {

            const path = window.location.pathname

            this.getValues(path);

        },

        computed: {

            ...mapGetters(['getApiKey'])
        },

        methods: {

            onChange(value, name) {
                if (name === 'ip_address') {
                    this.ipAddress = value
                }
                else if (name === 'comments') {
                    this.comments = value
                }
            },


            getValues(path) {

                const hostId = getIdFromUrl(path)

                if (path.indexOf('edit') >= 0) {

                    this.title = 'edit_banned_host'

                    this.iconClass = 'fas fa-sync'

                    this.btnName = 'update'

                    this.hasDataPopulated = false

                    this.getInitialValues(hostId);

                    this.hostId = hostId;

                    this.apiEndpoint = '/api/admin/bannedHosts/edit';

                } else {

                    this.loading = false;

                    this.hasDataPopulated = true;

                    this.apiEndpoint = '/api/admin/bannedHosts/add';
                }
            },
            getInitialValues(id) {

                this.loading = true

                axios.get('/api/admin/viewBannedHost/' + id).then(res => {

                    this.loading = false;

                    this.hasDataPopulated = true

                    this.updateStatesWithData(res.data.data.banned_host_data);

                }).catch(error => {

                    this.loading = false;
                });
            },

            updateStatesWithData(data) {
                if (data.banned_host_ip) {
                    this.ipAddress = data.banned_host_ip
                }
                if (data.banned_host_comments) {
                    this.comments = data.banned_host_comments
                }

            },

            onSubmit() {
                if (this.ipAddress) {

                    this.loading = true

                    const formData = {
                        banned_host_ip: this.ipAddress,
                        banned_host_comments: this.comments,
                        api_key_secret: this.getApiKey
                    }

                    if (this.hostId) {

                        formData['banned_host_id'] = this.hostId;
                    }

                    axios.post(this.apiEndpoint, formData).then(res => {

                        this.loading = false

                        successHandler(res, 'banned-hosts')

                        if (!this.hostId) {

                            setTimeout(() => {

                                this.$router.push('/banned-hosts/list')

                            }, 2000)

                        } else {

                            this.getInitialValues(this.hostId)
                        }

                    }).catch(err => {

                        this.loading = false

                        errorHandler(err, 'banned-hosts')
                    });
                }
            }
        },

        components: {

            "text-field": require("components/Reusable/FormField/TextField").default,
        }
    }
</script>
<template>
    <div class="col-sm-12">
        <alert componentName="RedisStreamSettings" />

        <div class="row" v-if="loading">
            <custom-loader :duration="4000"></custom-loader>
        </div>

        <div class="card card-light" v-if="!loading">
            <div class="card-header">
                <h3 class="card-title">Redis Stream Settings</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <text-field :label="'Host'" :value="form.stream_redis_host" :onChange="onChange"
                                name="stream_redis_host" type="text" classname="col-sm-6" :required="true">
                    </text-field>

                    <number-field :label="'Port'" :value="form.stream_redis_port" :onChange="onChange"
                                  name="stream_redis_port" type="number" classname="col-sm-6" :required="true">
                    </number-field>
                </div>

                <div class="row">
                    <text-field :label="'Username'" :value="form.stream_redis_username" :onChange="onChange"
                                name="stream_redis_username" type="text" classname="col-sm-6" :required="false">
                    </text-field>

                    <text-field :label="'Password'" :value="form.stream_redis_password" :onChange="onChange"
                                name="stream_redis_password" type="password" classname="col-sm-6" :required="false">
                    </text-field>
                </div>

                <div class="row">
                    <number-field :label="'Database'" :value="form.stream_redis_database" :onChange="onChange"
                                  name="stream_redis_database" type="number" classname="col-sm-6" :required="true">
                    </number-field>
                </div>
            </div>

            <div class="card-footer">
                <button class="btn btn-primary" @click="onSubmit()" :disabled="saving">
                    <i :class="saving ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>&nbsp;&nbsp;{{ saving ? 'Saving...' : 'Save' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { errorHandler, successHandler } from '../../helpers/responseHandler';
import TextField from "../../components/Reusable/FormField/TextField.vue";
import NumberField from "../../components/Reusable/FormField/NumberField.vue";

export default {
    name: 'RedisStreamSettings',

    data() {
        return {
            loading: false,
            saving: false,
            form: {
                stream_redis_host: '127.0.0.1',
                stream_redis_port: '6379',
                stream_redis_username: '',
                stream_redis_password: '',
                stream_redis_database: '2',
            },
        };
    },

    beforeMount() {
        this.fetchSettings();
    },

    methods: {
        fetchSettings() {
            this.loading = true;
            axios.get('/api/admin/stream-settings')
                .then((res) => {
                    this.form = res.data.data;
                })
                .catch((error) => {
                    errorHandler(error, 'RedisStreamSettings');
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        onChange(value, name) {
            if (this.form.hasOwnProperty(name)) {
                this.form[name] = value;
            }
        },

        onSubmit() {
            this.saving = true;
            axios.post('/api/admin/stream-settings', this.form)
                .then((response) => {
                    successHandler(response, 'RedisStreamSettings');
                })
                .catch((error) => {
                    errorHandler(error, 'RedisStreamSettings');
                })
                .finally(() => {
                    this.saving = false;
                });
        },

    },

    components: {
        "text-field": TextField,
        "number-field": NumberField,
    },
};
</script>

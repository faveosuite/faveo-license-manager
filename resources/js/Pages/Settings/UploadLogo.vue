<template>
    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>{{lang('add_new_logo')}}</span>
        </div>

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{title}}</h3>
                <alert componentName="upload"></alert>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('logo_name')" :value="logo_name" classname="col-sm-6">
                    </text-field>

                    <label for="logo_image">Logo Image:</label>
                    <input type="file" id="logo_image" @change="onImageChange" accept="image/*">

                </div>
            </div>


            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit">
                    <i :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>


        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{title}}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <text-field :label="lang('sidebar_name')" :value="logo_name" classname="col-sm-6">

                    </text-field>

                    <label for="sidebar_logo_image">Sidebar Logo Image:</label>
                    <input type="file" id="sidebar_logo_image" @change="onImageChange" accept="image/*">
                </div>
            </div>

            <div class="card-footer">

                <button class="btn btn-primary" @click="onSubmit">
                    <i :class="iconClass"></i>&nbsp;&nbsp;{{lang(btnName)}}</button>
            </div>
        </div>
    </div>
</template>
<script>

import axios from "axios";
import TextField from "../../components/Reusable/FormField/TextField.vue";
import {errorHandler, successHandler} from "../../helpers/responseHandler";

export default{

    name: 'Upload-logo',

    data() {

        return{

            title:'Upload Logo',

            iconClass: 'fas fa-save',

            btnName: 'save',

            logo: "",

        }
    },

    methods : {
        onSubmit(){
            axios.post('api/admin/store-logo-settings', {
                'logo_title' : this.logo_name,
                'login_image' : this.logo,
            }).then( res => {
                  this.loading =false;
                successHandler(res, 'upload');
            }).catch(err => {
                this.loading =false;
                errorHandler(err, 'upload');

            })
        },


        onImageChange(event) {
            const file = event.target.files[0];
            this.logo = file;
        },

        lang(key) {
            return `Mock translated value for ${key}`;
        },
    },

    components:{

        TextField
    }
}
</script>

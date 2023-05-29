<template>
    <div class="col-sm-12">

        <div class="alert alert-info">
            <span>Add new logo</span>
        </div>

<!--        <div class="row" v-if="!hasDataPopulated || loading">-->

<!--            <custom-loader :duration="4000"></custom-loader>-->
<!--        </div>-->

        <div class="card card-light">

            <div class="card-header">

                <h3 class="card-title">{{title}}</h3>
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

export default{

    name: 'Upload-logo',

    data() {

        return{

            title:'Upload Logo',

            iconClass: 'fas fa-save',

            btnName: 'save',

        }
    },

    methods : {
        onSubmit(){
            axios.post('api/admin/store-logo-settings', {
                'logo_title' : this.logo_name,
                'login_image' : this.logo,
            }).then( res => {
                alert('sent')
            }).catch(err => {
                alert('errror')
            })
        },

        onImageChange(){
            this.logo = event.target.value;
        },

        lang(key) {
            // You can customize this mock implementation based on your localization needs
            return `Mock translated value for ${key}`;
        },
    },

    components:{

        TextField
    }
}
</script>

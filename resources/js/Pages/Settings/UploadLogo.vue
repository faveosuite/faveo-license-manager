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

                    <text-field :label="lang('logo_name')" :value="logo_name" :onChange="onTextFieldChange" classname="col-sm-6"></text-field>

                    <label for="logo_image">Logo Image:</label>
                    <input type="file" id="sidebar_logo_image" @change="onImageChange" accept="image/*">


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

                    <text-field :label="lang('sidebar_name')" :onChange="onTextFieldChangee" :value="logo_namee" classname="col-sm-6">

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
import { errorHandler, successHandler } from "../../helpers/responseHandler";

export default {
    name: 'Upload-logo',

    data() {
        return {
            title: 'Upload Logo',
            iconClass: 'fas fa-save',
            btnName: 'save',
            logo_name: "", // Updated to include logo_name property
            logo_namee: "",
            logo: null, // Updated to initialize logo as null
        };
    },

    beforeMount() {
        // this.getValues();
    },

    methods: {
        onTextFieldChange(newValue) {
            // Handle the new value here
            this.logo_name = newValue;
        },

        onTextFieldChangee(newValue){
            // Handle the new value here
            this.logo_name = newValue;
        },

        onSubmit() {
            const formData = new FormData(); // Create a new FormData object
            formData.append("logo_title", this.logo_name); // Append the logo_title field

            formData.append("logo_image", this.logo); // Append the logo_image field

            axios.post('api/admin/store-logo-settings', formData)
                .then(res => {
                    this.loading = false;
                    alert(res);
                    successHandler(res, 'upload');
                })
                .catch(err => {
                    this.loading = false;
                    alert(err);
                    errorHandler(err, 'upload');
                });
        },
        //
        // getValues() {
        //     axios.get('api/admin/getlogos').then(res => {
        //         let data = res.data.data;
        //         alert(res.data.data);
        //     });
        // },

        onImageChange(event) {
            this.logo = event.target.files[0];
        },


        lang(key) {
            return `Mock translated value for ${key}`;
        },
    },

    components: {
        TextField,
    },
};
</script>

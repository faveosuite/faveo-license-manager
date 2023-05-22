<template>
<div class="col-sm-12">
    <div class="row" v-if="!hasDataPopulated || loading">

        <custom-loader :duration="4000"></custom-loader>
    </div>

    <alert componentName="Upload Logo"/>

    <div class="card card-light" v-if="hasDataPopulated">

        <div class="card-header">

            <h3 class="card-title">{{(title)}}</h3>
        </div>

        <div class="card-body">

            <div class="row">

                <text-field :label="lang('Title')" :value="title" type="text" name="title"
                             classname="col-sm-6" :required="true">

                </text-field>

                <text-field :label="lang('Type')" :value="type" type="text" name="type"
                             classname="col-sm-6" :required="true">

                </text-field>
            </div>

        </div>
    </div>
</div>
</template>

<script>
import TextField from "../../components/Reusable/FormField/TextField.vue";

export default{
    name : 'upload-logo',

    data(){
        return{
            title: 'upload_logo',

            hasDataPopulated: false,

            loading: false,
        }
    },

    methods:{

    },

    components:{
        TextField
    }
}
</script>

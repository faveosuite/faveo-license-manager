<template>

    <div>
        <div class="center-of-page">
            <spinner :animation-duration="duration" :size="60" :color="color" ></spinner>
        </div>
        <div class="full-page"></div>
    </div>
</template>

<script>
    import { FulfillingBouncingCircleSpinner } from 'epic-spinners';

    export default {

        components:{ 'spinner':FulfillingBouncingCircleSpinner},

        props:{
        
            duration:{type:Number, default: 4000},
        
            color : { type : String, default : '#1d78ff'}
        }
    };
</script>

<style type="text/css">
    .center-of-page{
        width: 100px;
        height: 100px;
        text-align: center;
        position: fixed;
        top: 25%;
        left: 25%;
        transform: translate(-50%, -50%);
        transform: -webkit-translate(-50%, -50%);
        transform: -moz-translate(-50%, -50%);
        transform: -ms-translate(-50%, -50%);
        bottom: 0;
        right: 0;
        z-index:100000;
        margin: auto;
    }
</style>
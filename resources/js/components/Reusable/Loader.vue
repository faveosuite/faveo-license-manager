
<template>
	
    <fulfilling-bouncing-circle-spinner :animation-duration="duration" :size="size" :color="color"/>
</template>

<script>

    import { FulfillingBouncingCircleSpinner } from 'epic-spinners';
    
    export default{

        props:{

            duration:{type:Number, default: 4000},

            size : { type : Number | String, default : 60},

            color : { type : String , default : '#1d78ff'},
        },

    	components: {

            'fulfilling-bouncing-circle-spinner':FulfillingBouncingCircleSpinner,
        }
    };
</script>

<style scoped>

    .fulfilling-bouncing-circle-spinner{
        margin: auto !important;
        left :0;
    }
</style>

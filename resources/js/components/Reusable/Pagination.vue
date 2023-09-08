<template>

    <div class="simple-pagination d-flex float-right">

        <button class="btn btn-primary m-2" @click="onPagination('previous')" :disabled="true">

            <i class="fas fa-arrow-left"></i> &nbsp; {{ trans('previous') }}
        </button>

        <button class="btn btn-primary m-2" @click="onPagination('next')" :disabled="false">

            {{ trans('next') }} &nbsp;<i class="fas fa-arrow-right"></i>
        </button>
    </div>
</template>

<script>
export default {
    name : 'simple-pagination',
    props : {
        prev_page_url : { type : [String,null], default : '' },
        next_page_url : { type : [String,null], default : '' },
        onPagination : { type : Function, default : ()=>{} }
    }
}
</script>

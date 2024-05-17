<template>

    <div>

        <modal v-if="showModal" :showModal="showModal" :onClose="onClose" :containerStyle="containerStyle">

            <template #title>

                <h4 class="modal-title">{{lang(title)}}</h4>
            </template>

            <template #fields>

                <div v-if="loading" class="row">

                    <reuse-loader :animation-duration="4000" color="#1d78ff" :size="size" />
                </div>

                <div v-if="!loading" class="mod_width">

                    <p v-html="content" :class="[{'trace' : title === 'trace'}]"></p>
                </div>


            </template>

            <template #controls>

<!--                <div v-if="title === 'delete_logs'">-->
<!--                    <button id="log_delete_btn" type="button" @click = "onSubmit()" class="btn btn-danger" :disabled="isDisabled"><i class="fa fa-trash" aria-hidden="true"></i> {{lang('delete')}}</button>-->
<!--                </div>-->
            </template>
        </modal>
    </div>
</template>

<script>

import moment from 'moment';
import axios from 'axios';
import {errorHandler,successHandler} from "../../helpers/responseHandler";

export default {

    name : 'logs-modal',

    description : 'Logs Modal component',

    props:{

        showModal:{type:Boolean,default:false},

        onClose:{type: Function},

        data : { type : Object , default:()=>{}},

        title : { type : String , default :''},

        hideCheckBox: {type:Boolean}

    },

    data:()=>({

        containerStyle:{
            width:'950px'
        },
        shouldHideCheckBox :true,
        /**
         * initial state of loader
         * @type {Boolean}
         */
        loading:true,

        /**
         * size of the loader
         * @type {Number}
         */
        size: 60,

        /**
         * for rtl support
         * @type {String}
         */
        lang_locale:'',

        content : '',

        mail_message : '' ,

        isDisabled : true,

        delete_after_date : '',

        delete_before_date : '',

        timeOptions:{
            start: '00:00',
            step: '00:30',
            end: '23:30'
        },

        checkOptions:[
            {name:'cron',value:'cron'},
            {name:'mail',value:'mail'},
            {name:'exception',value:'exception'}
        ],

        logs : []

    }),

    beforeMount() {

        this.checkTitle();
    },

    created(){

        this.lang_locale = localStorage.getItem('LANGUAGE');
    },

    methods:{

        // contentParser(value) {
        //
        //     var parser = new DOMParser();
        //
        //     var doc = parser.parseFromString(value, 'text/html');
        //
        //     const root = doc.getElementsByTagName('body')[0];
        //
        //     const anchors  = root.querySelectorAll('a');
        //
        //     anchors.forEach(anchor => {
        //
        //         let anchorArr = anchor.href.replace(this.basePath(),'').split('/');
        //
        //         let modifiedArr = anchorArr.filter(item => item);
        //
        //         if(modifiedArr[0] == 'change-approval' || (modifiedArr[0] == 'service-desk' && modifiedArr[1] == 'contract-approval')) {
        //
        //             anchor = anchor;
        //
        //         } else {
        //
        //             const span = doc.createElement('a');
        //
        //             if(!anchor.href.includes('/panel/') && !anchor.href.includes('/ticket-conversation/') && !anchor.href.includes('/agent/export/download/') && !anchor.href.includes('/check-ticket/')) {
        //
        //                 span.setAttribute('href',anchor.href.replace(this.basePath(),this.basePath()+'/panel'));
        //
        //             } else {
        //
        //                 span.setAttribute('href',anchor.href);
        //             }
        //
        //             span.innerText = anchor.innerText;
        //
        //             anchor.parentNode.replaceChild(span, anchor);
        //
        //         }
        //     });
        //
        //     return root.innerHTML;
        // },

        checkTitle(){
            if(this.title !== 'delete_logs'){

                if(this.title === 'logs_content'){

                    this.getLogsContent(this.data.id)
                } else{

                    this.content = this.data.trace

                    this. containerStyle.width = "1000px"

                    this.loading = false;
                }
            } else{
                this.loading = false
            }
        },

        getLogsContent(id){

            axios.get('/api/get-log-mail-body/'+id).then(res=>{

                this.loading = falseLogsTrace

                this.content = this.contentParser(res.data.data.mail_body.replaceAll('<br />\r\n', ""));

                this.mail_message = res.data.message;
            }).catch(err => {

                this.loading = false
            })
        },

        // onChange(value,name){
        //
        //     this[name] = value;
        //
        //     if(name === 'logs'){
        //         this.isDisabled = value.length === 0 ? true : false
        //     }
        // },

        // onSubmit(){
        //     //for delete
        //     this.loading = true
        //     this.isDisabled = true
        //
        //     const data={};
        //     data['categories'] = this.logs;
        //     data['delete_before']=this.delete_before_date !== ''  && this.delete_before_date !== null ? moment(this.delete_before_date).format('YYYY-MM-DD HH:mm:ss') : '';
        //     data['delete_after']=this.delete_after_date !== '' && this.delete_after_date !== null ? moment(this.delete_after_date).format('YYYY-MM-DD HH:mm:ss') : '';
        //     axios.delete('/api/delete-logs',{data}).then(res=>{
        //         successHandler(res,'dataTableModal');
        //         window.emitter.emit('cronLogsrefreshData');
        //         window.emitter.emit('mailLogsrefreshData');
        //         window.emitter.emit('exceptionLogsrefreshData');
        //         this.onClose();
        //         this.loading = false;
        //         this.isDisabled = true
        //     }).catch(err => {
        //         errorHandler(err,'dataTableModal')
        //     })
        // },

    },

};
</script>

<style type="text/css">

.mod_width{
    max-height: 400px;
    overflow-x: hidden;
    overflow-y: auto;
}
.trace {
    background: black !important;
    color: aliceblue;
    padding: 10px;
    font-family: monospace;
    font-size: 13px;
    line-height: 1.5 !important;
}

p{
    word-break:break-word;
}
</style>

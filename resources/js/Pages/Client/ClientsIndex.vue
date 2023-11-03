<template>

	<div class="col-sm-12">
   
	<div class="row" v-if="loading">
   
	<custom-loader :duration="4000"></custom-loader>
	</div>
   
	<alert componentName="dataTableModal" />
   
	<div class="card card-light ">
   
	<div class="card-header">
   
	<h3 class="card-title">{{lang('contacts')}}</h3>
   
	<div class="card-tools">
   
	<router-link to="/clients/create" class="btn-tool" v-tooltip="lang('create_client')">
   
	<i class="fas fa-plus"></i>
	</router-link>
	</div>
	</div>
   
	<div class="card-body" id="my_clients">
   
	<v-client-table v-if="data" :columns="columns" :data="data" :options="options" :key="counter">
   
	<template v-slot:full_name="props">
   
	<router-link :to="'/clients/' + props.row.client_id + '/edit'">{{props.row.full_name}}</router-link>
	</template>
	<template v-slot:client_email="props">
   
   <router-link :to="'/clients/' + props.row.client_id + '/edit'">{{props.row.client_email}}</router-link>
   </template>
   
	<template v-slot:client_status="props">
   
	<span :style="{ color: props.row.client_status ? 'green' : 'red' }">
   
	{{ props.row.client_status ? 'Active' : 'Inactive'}}
	</span>
	</template>
   
	<template v-slot:actions="props">
		<table-actions :data="props.row" :disabled="getUserData"></table-actions>
	</template>
	</v-client-table>
	</div>
	</div>
	</div>
   </template>
   
   <script>
   
	import axios from 'axios';
	import {useStore} from "vuex";
	import {computed} from "vue";
   
	export default {
	setup() {
   
	const store = useStore();
   
	return {
	// getter
	getUserData: computed(() => store.getters.getUserData)
	};
	},
	data() {
   
	return {
   
	data: '',
   
	columns: ['full_name', 'client_email', 'client_role' , 'client_active_date', 'client_status', 'actions'],
   
	options: {},
   
	counter: 0,
   
	disabled:true,
	}
	},
   
	created() {
   
	this.emitter.on('refreshData', this.updateData);
	},
	beforeMount() {

 const self = this;

 this.getData();

 this.options = {

 sortIcon: {

 base: 'glyphicon',

 up: 'glyphicon-chevron-up',

 down: 'glyphicon-chevron-down'
 },

 texts: { filter: '', limit: '' },

 columnsClasses: {

 full_name: 'full_name',

 client_email: 'client_email',

 client_active_date: 'client_date',

 client_status: 'client_status',

 client_role: 'client_role'
 },

 templates: {},

 pagination: { chunk: 5, nav: 'fixed', edge: true },

 headings: {

 full_name: 'Name',

 client_email: 'Email',

 client_active_date: 'Active Date',

 client_status: 'Status',

 client_role: 'Role',

 actions: 'Actions'
 },
 }
 },

 methods: {

 updateData() {

 this.getData();
 },

 getData() {

 this.loading = true;

 axios.get('/api/admin/viewClients/' + this.getUserData.client_id)
 
.then(res => {
 this.loading = false;

 this.data = res.data.data.map(data => {

 data.edit_url = '/clients/' + data.client_id + '/edit';

 data.delete_url = '/api/admin/clients/delete';

 data.keyVal = 'client_id';

 data.idVal = data.client_id;

 return data;
 })
 }).catch(err => {

 this.loading = false;
 })
 }
 }
 };
</script>

<style>
 .client_name,
 .client_email,
 .client_date,
 .client_status {
 max-width: 200px;
 word-break: break-all;
 }
 .client_role{
 text-transform: capitalize;
 }

 #my_clients .VueTables .table-responsive {
 overflow-x: auto;
 overflow-y: hidden;
 }

 #my_clients .VueTables .table-responsive>table {
 width: max-content;
 min-width: 100%;
 max-width: max-content;
 overflow: auto !important;
 }

</style>
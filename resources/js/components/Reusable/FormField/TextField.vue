<template>

	<form-field-template :label="label" :labelStyle="labelStyle" :name="name" :classname="classname" :hint="hint" :required="required"
		:showNewButton="showNewButton" :newBtnName="newBtnName" :onClickEvent="getActionEvent">

		<template>

			<div v-if="type === 'textarea'">

				<textarea :id="id" :name="name" 
					:class="['form-control', inputClass]" 
					:maxlength="length" 
					:type="type" 
					v-model="changedValue" 
					v-on:input="onChange(changedValue, name)" 
					:cols="columns" 
					:rows="rows" 
					:style="inputStyle" 
					:placeholder="placehold">
							
				</textarea>
			</div>

			<input v-else :id="id" :name="name" 
				:class="['form-control', inputClass]" 
				:type="type" 
				:disabled="disabled" 
				:style="inputStyle" 
				v-model="changedValue" 
				v-on:input="onChange(changedValue, name)" 
				@keyup="keyupListener($event,name)"
				@keydown="keydownListener($event,name)" 
				@keypress="keypressEvt($event,name)" 
				@paste="pasteEvt($event,name)"
				:placeholder="placehold" 
				:maxlength="max ? max : undefined" 
			/>
		</template>
	</form-field-template>
</template>

<script>

	import { boolean } from "helpers/extraLogics";

	export default {
		
		name: "text-field",
		
		props: {
			
			label: { type: String, required: true },

			hint: { type: String, default: "" }, //for tooltip message

			value: { type: String|null, required: true },
		
			name: { type: String, required: true },

			type: { type: String, default: "text" },

			onChange: { type: Function, Required: true },

			classname: { type: String, default: "" },

			required: { type: Boolean, default: false },

			length: {type: Number|String, default: 2000},

			keyupListener: { type: Function , default : ()=>{} },

			keydownListener: { type: Function , default : ()=>{} },

			keypressEvt: { type: Function ,  default : ()=>{} },

			pasteEvt: { type: Function ,  default : ()=>{} },
	
			labelStyle:{type:Object},

			placehold : { type: String, default : 'Enter a value'},

			id : {type: String|Number, default:'text-field'},

			disabled : { type : Boolean, default : false},

			columns : { type : String | Number, default : ''},

			inputStyle : { type : Object, default : ()=>{}},

			max : { type : Number | String , default : ''},

			rows : { type : Number | String , default : ''},

			cols : { type : Number | String , default : ''},

			inputClass : { type : String, default : ''},

			showWordLimit: { type: Boolean, default: false },

			showNewButton: { type: Boolean, default: false },

			newBtnName : { type : String, default : '' },

			onNewButtonClick: { type : Function, default : ()=>{}},

		},

		data() {
			
			return {
			
				changedValue: this.value
			};
		},

		mounted() {
			
			this.changedValue = this.value;
		},

		watch: {
			
			value(newVal) {
				
				this.changedValue = newVal;
			}
		},

		methods : {
			
			getActionEvent(name){
						
				this.onNewButtonClick(name)
			},
		},

		components: {
			
			"form-field-template": require("./FormFieldTemplate").default,
		}
	};
</script>

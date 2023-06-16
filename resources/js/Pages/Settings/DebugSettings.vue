<template>
  <div class="col-sm-12">
    <div class="alert alert-info">
      <p>Configure Debugger settings, enable and disable individual options.</p>
    </div>
    <alert componentName="settings" />
    <div class="card card-light">
      <div class="card-header">
        <h3 class="card-title">Debugger Settings</h3>
      </div>

      <div class="card-body">
        <div class="row">
          <label>
            <input type="radio" value="1" v-model="selectedValue" />
            Enable &nbsp;&nbsp;&nbsp;
          </label>
          <label>
            <input type="radio" value="0" v-model="selectedValue" />
            &nbsp;Disable
          </label>
        </div>
      </div>

      <div class="card-footer">
        <button type="submit" class="btn btn-primary" @click="saveValue">
          <i :class="iconClass"></i>&nbsp;&nbsp;{{ lang(btnName) }}
        </button>
      </div>
    </div>
  </div>
</template>
  
  <script>
import axios from "axios";

export default {
  data() {
    return {
      selectedValue: "", 
      debugValue: "", 
      iconClass: "fas fa-save",
      btnName: "save",
    };
  },

  created() {
    this.debugValue = localStorage.getItem("debug") || ""; 
    this.selectedValue = this.debugValue; 
  },

  mounted() {
    window.addEventListener("beforeunload", this.saveToLocalStorage); 
  },

  methods: {
    saveToLocalStorage() {
      localStorage.setItem("debug", this.selectedValue); 
    },
    saveValue() {
      const data = {
        debug: this.selectedValue, 
      };
      axios
        .post("/api/save-debug-value", data)
        .then((response) => {
          this.debugValue = response.data.debug;
          localStorage.setItem("debug", this.debugValue);
          console.log("Value saved successfully!");
          setTimeout(() => {
            window.location.reload(); 
          }, 500);
        })
        .catch((error) => {
          console.error("Failed to save value:", error);
        });
    },
  },
};
</script>
  

  
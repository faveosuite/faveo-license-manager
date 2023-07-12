<template>
  <div class="col-sm-12">
    <div class="alert alert-info">
      <p>Configure Debugger settings, enable and disable individual options.</p>
    </div>
    <div class="card card-light">
      <div class="card-header">
        <h3 class="card-title">Debugger Settings</h3>
      </div>

      <div class="card-body">
        <div class="row">
          <div class="col-6">
            <label>
              <input type="radio" value="1" v-model="selectedValue" />
              Enable &nbsp;&nbsp;&nbsp;
            </label>
            <label>
              <input type="radio" value="0" v-model="selectedValue" />
              &nbsp; Disable
              <input type="hidden" name="user_id" v-model="user_id" />
            </label>
          </div>
          <div class="col-6">
            <div v-if="showLink">
              <a href="/lakshya/public/clockwork/app" style="margin-left: 5px">Clockwork</a><br />
            </div>
          </div>
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
      showLink: false,
      user_id: 0,
    };
  },

  created() {
    this.debugValue = localStorage.getItem("debug") || "";
    this.selectedValue = this.debugValue;
    this.showLink = localStorage.getItem("showLink") === "true" || false;
    this.user_id = localStorage.getItem("user_id") || 0;
    this.saveTokenForDebugger(); // Call the API on component creation
  },

  methods: {
    saveValue() {
      const data = {
        debug: this.selectedValue,
        user_id: this.user_id,
      };

      axios
        .post("/api/save-debug-value", data)
        .then((response) => {
          this.debugValue = response.data.debug;
          localStorage.setItem("debug", this.debugValue);

          this.showLink = this.selectedValue === "1";
          localStorage.setItem("showLink", this.showLink);

          this.saveTokenForDebugger(); // Call the API after saving the value

          setTimeout(() => {
            window.location.reload();
          }, 500);
        })
        .catch((error) => {
        });
    },

    saveTokenForDebugger() {
      const data = {
        getdebug: this.selectedValue,
        user_id: this.user_id,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      };

      axios
        .post("/api/saveTokenForDebugger", data)
        .then((response) => {
          this.selectedValue = this.debugValue;
                            localStorage.setItem("debug", this.selectedValue);
                        })
                        .catch((error) => {
                        });
    },
  },
};
</script>

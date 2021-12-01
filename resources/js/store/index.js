import Vue from 'vue';

import Vuex from 'vuex';

import auth from './modules/auth';

import alert from './modules/alert';

import VuexPersist from 'vuex-persist';

Vue.use(Vuex);

const vuexLocalStorage = new VuexPersist({
  // storage: window.localStorage, 
  reducer: state => ({
    auth: state.auth
  })
})

export const store = new Vuex.Store({
  modules : {
    auth,
    alert
  },
  plugins: [vuexLocalStorage.plugin]
})


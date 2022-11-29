import { createStore } from "vuex";

import VuexPersist from 'vuex-persist';

import auth from './modules/auth';

import alert from './modules/alert';

import setting from './modules/setting';

const vuexLocalStorage = new VuexPersist({
    // storage: window.localStorage,
    reducer: state => ({
        auth: state.auth
    })
})

const store = createStore({

    modules : {
        auth,
        alert,
        setting
    },

    plugins: [vuexLocalStorage.plugin]
});

export default store;

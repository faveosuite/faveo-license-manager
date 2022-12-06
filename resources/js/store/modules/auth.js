
const state = {

    user_token : '',

    user_data : '',

    api_key : '',
 };


 const getters = {

    getUserToken: state => state.user_token,

    getUserData: state => state.user_data,

    getApiKey : state => state.api_key
 };

 const mutations = {

    updateUserToken(state, payload) {

        state.user_token = payload;
    },

    updateUserInfo(state,payload) {

        state.user_data = payload
    },

    updateApiKey(state,payload) {

        state.api_key = payload
    }
 }

 const actions = {

    setLoggedInUserToken({commit},payload) {

        commit('updateUserToken',payload)
    },

    setUserInfo({commit},payload) {
        commit('updateUserInfo',payload)
    },

    setApiKey({commit}) {

        axios.get('/api/admin/viewApiKeys').then(res => {

            commit('updateApiKey',res.data.data[0].api_key_secret)

        }).catch(err => {

            commit('updateApiKey','')

            // if(err.response){
            //
            //     errorHandler(err);
            // }
        });

    }
 }

 export default {state, getters, mutations, actions}


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
    },

     updateUserData(state, payload)  {

         state.user_data.client_profile_pic = payload.profile_pic
         state.user_data.client_mobile_code = payload.client_mobile_code
         state.user_data.client_iso2 = payload.client_iso2
     }
 }

 const actions = {

    setLoggedInUserToken({commit},payload) {

        commit('updateUserToken',payload)
    },

    setUserInfo({commit},payload) {
        commit('updateUserInfo',payload)
    },

    setUserData({commit}, payload) {
        commit('updateUserData', payload)
    },

    setApiKey({commit}) {

        axios.get('/api/admin/viewApiKeys').then(res => {

            commit('updateApiKey',res.data.data.data[0].api_key_secret)

        }).catch(err => {

            commit('updateApiKey','')
        });

    }
 }

 export default {state, getters, mutations, actions}

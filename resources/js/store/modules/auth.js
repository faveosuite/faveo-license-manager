
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
        console.log(payload,'token==============')
        commit('updateUserToken',payload) 
    },

    setUserInfo({commit},payload) {
        console.log(payload,'info==============')

        commit('updateUserInfo',payload)
    },
    setApiKey({commit},payload) {
        commit('updateApiKey',payload)
    }
 }
 
 export default {state, getters, mutations, actions}
 
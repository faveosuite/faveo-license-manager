// import { mount } from '@vue/test-utils';
// import { createStore } from 'vuex';
// import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
// import axios from 'axios';
// import MockAdapter from 'axios-mock-adapter';
//
// describe('ClientsIndex', () => {
//     // Create a mock Vuex store
//     const store = createStore({
//         getters: {
//             getUserData: () => ({ client_id: 12}),
//         },
//     });
//     const emitter = {
//         on: jest.fn(),
//     };
//
//     it('renders component correctly', async () => {
//         const wrapper = mount(ClientsIndex, {
//             global: {
//                 plugins: [store],
//                 mocks: {
//                     emitter,
//                 },
//             },
//         });
//         await wrapper.vm.$nextTick();
//
//         expect(wrapper.exists()).toBe(true);
//     });
//
//     it('renders without errors', () => {
//         const wrapper = mount(ClientsIndex, {
//             global: {
//                 plugins: [store],
//                 mocks: {
//                     emitter,
//                 },
//             },
//         });
//         expect(wrapper.exists()).toBe(true);
//     });
//
// });

import { mount } from '@vue/test-utils';

import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
import {createStore} from "vuex";

describe('ClientsIndex', () => {

    let wrapper;

    let store;

    let actions;

    let getters;

    getters = {

        getUserData : ()=>{
            return {
                client_id : 1
            }
        }
    }

    actions = { unsetValidationError: jest.fn() }

    store = createStore({ getters, actions })

    beforeEach(()=>{

        wrapper = mount(ClientsIndex,{

            global : { plugins : [store], stubs:['data-table'] },

            props : {generalSetting : {
                    time_format : {js_format:81},
                    timezone : {name : 'Asia/Kolkata'},
                    date_format : {js_format : 8765}
                }}

        })
    })

    it('data-table should exists when page created', async () => {

        await expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("return row->created_at for `created_at` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.client_active_date('test', {'client_active_date': '2012-10-12'})).toEqual("8765 81")
    })

    it("requestAdapter method should return `sort_field`, `sort_order`, `search_query` & `limit`", () => {
        let reqAdptData = {
            "orderBy": "id",
            "ascending": true,
            "query": "something",
            "limit": 10
        }
        let reqAdptDataReturn = {
            "sort_field": "id",
            "sort_order": "asc",
            "search_query": "something",
            "perPage": 10,
        }
        expect(wrapper.vm.options.requestAdapter(reqAdptData)).toEqual(reqAdptDataReturn)
    });

    it("`responseAdapter` set edit_url, delete_url and view_url to the data property", () => {

        let responseAdpData = {
            "data": {
                "data": {
                    "data": [
                        {client_id: 1},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{"edit_url": "/clients/1/edit", "client_id":1, "delete_url" : "/api/admin/clients/delete", "keyVal": "client_id", "idVal": 1}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

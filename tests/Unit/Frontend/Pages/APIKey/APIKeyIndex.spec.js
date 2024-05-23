//
// import {mount, shallowMount} from '@vue/test-utils';
// import APIKeyIndex from "../../../../../resources/js/Pages/APIKey/APIKeyIndex.vue";
// import axios from "axios";
// import MockAdapter from "axios-mock-adapter";
// describe('ProductsIndex', () => {
//
//     const emitter = {
//         on: jest.fn(),
//     };
//
//     it('renders without errors', () => {
//         const wrapper = mount(APIKeyIndex, {
//             global: {
//                 mocks: {
//                     emitter,
//                 },
//             },
//         });
//         expect(wrapper.exists()).toBe(true);
//     });
//
//     it('fetches data from the API correctly',async() =>{
//         const mock = new MockAdapter(axios);
//         const responseData = {
//             data: {
//                 api_key_licenses_add: 1,
//                 api_key_licenses_edit: 1,
//                 api_key_products_add: 1,
//                 api_key_products_edit: 1,
//                 api_key_search: 1,
//                 api_key_secret: "5hDuaXuTh9gTLfPL",
//             },
//         };
//         mock.onGet('/api/admin/viewApiKeys').reply(200, responseData);
//         const wrapper = mount(APIKeyIndex, {
//             global: {
//                 mocks: {
//                     emitter,
//                 },
//             },
//         });
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.loading).toBe(false);
//
//     })
// });

import { mount } from '@vue/test-utils';

import APIKeyIndex from "../../../../../resources/js/Pages/APIKey/APIKeyIndex.vue";

import {createStore} from "vuex";

describe('APIKeyIndex', () => {

    let wrapper;

    let store;

    let actions;

    let getters;

    getters = {

        formattedTime: () => () => {return ''}
    }

    actions = { unsetValidationError: jest.fn() }

    store = createStore({ getters, actions })

    beforeEach(()=>{

        wrapper = mount(APIKeyIndex,{

            global : { plugins : [store], stubs:['custom-loader', 'alert', 'data-table'] },

        })
    })

    it('data-table should exists when page created', async () => {

        await expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("return row->created_at for `created_at` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.api_key_ip('test', {'api_key_ip': '2012-10-12'})).toEqual("2012-10-12")
    })

    it("requestAdapter method should return `sort-field`, `sort-order`, `search-query`, `page` & `limit`", () => {
        // page query will come with url
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
                        {api_key_id : 1, product_id : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "api_key_id":1, "keyVal" : "product_id" , "product_id":3, "edit_url": "/apikeys/1/edit", "delete_url": "/api/admin/deleteapi/1"}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})







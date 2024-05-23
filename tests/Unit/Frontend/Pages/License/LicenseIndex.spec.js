//
// import {mount, shallowMount} from '@vue/test-utils';
// import LicensesIndex from "../../../../../resources/js/Pages/License/LicensesIndex.vue";
// import axios from "axios";
// import MockAdapter from "axios-mock-adapter";
// describe('LicensesIndex', () => {
//
//     it('renders without errors', () => {
//         const wrapper = shallowMount(LicensesIndex);
//         expect(wrapper.exists()).toBe(true);
//     });
//
//     it('fetches data from the API correctly',async() =>{
//         const mock = new MockAdapter(axios);
//         const responseData = {
//             data: {
//
//                 latest_license: "2022-02-17",
//                 license_code: "5hDuaXuTh9gTLfPL",
//                 license_status: 1,
//                 product_title: "Helpdesk Enterprise"
//             },
//         };
//         mock.onGet('/api/admin/viewLicenses').reply(200, responseData);
//         const wrapper = mount(LicensesIndex);
//
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.loading).toBe(false);
//
//     })
// });

import { mount } from '@vue/test-utils';

import LicenseIndex from "../../../../../resources/js/Pages/License/LicensesIndex.vue";

import {createStore} from "vuex";

describe('LicenseIndex', () => {

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

        wrapper = mount(LicenseIndex,{

            global : { plugins : [store], stubs:['custom-loader', 'alert', 'data-table'] },

        })
    })

    it('data-table should exists when page created', async () => {

        await expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("return row->created_at for `created_at` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.license_code('test', {'license_code': '201210124567'})).toEqual("2012-1012-4567")
    })

    it("return row->created_at for `created_at` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.license_code('test', {})).toEqual("----")
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
                        {license_id : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "license_id" , "license_id":3, "edit_url": "/licenses/3/edit", "delete_url": "/api/admin/license/delete"}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

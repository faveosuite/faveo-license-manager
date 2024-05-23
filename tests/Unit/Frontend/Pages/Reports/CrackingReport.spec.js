// import { mount } from "@vue/test-utils";
// import ViewCrackingReports from "../../../../../resources/js/Pages/Report/ViewCrackingReports";
// import globalMixins from "../../../../../resources/js/globalMixins";
// import { createStore } from "vuex";
// import axios from "axios";
// import MockAdapter from "axios-mock-adapter";
// const store = createStore({});
//
// let wrapper;
// let mockAxios = new MockAdapter(axios);
// const fakeRequestData = {
//     'success':true,
//     'data':{}
// }
// describe("ViewCrackingReports", () => {
//     const updateWrapper = () => {
//         wrapper = mount(ViewCrackingReports, {
//             global: {
//                 plugins: [store],
//                 mixins: [globalMixins],
//                 stubs: ["data-table", "data-table-stub"],
//             },
//         });
//     };
//
//     beforeEach(() => {
//         updateWrapper();
//         mockAxios.reset();
//     });
//
//     afterEach(() => {
//         mockAxios.restore();
//     });
//
//     it("makes an API call when 'getData' method  called", async() => {
//         updateWrapper();
//
//         stubRequest();
//         await wrapper.vm.getData()
//         setTimeout(() => {
//             expect(wrapper.vm.loading).toBe(false);
//             expect(wrapper.vm.data).toEqual('fakeRequestData');
//             expect(mockAxios.history.get[0].url).toBe("/api/admin/reportCracking");
//             done()
//         }, 10)
//     });
//
//
//     it("makes `loading` as false when api returns error", async () => {
//         updateWrapper();
//
//         stubRequest(400);
//
//         await wrapper.vm.getData();
//         setTimeout(() => {
//             expect(wrapper.vm.loading).toEqual(false)
//             expect(wrapper.vm.data).toEqual('');
//             expect(mockAxios.history.get[0].url).toBe("/api/admin/reportCracking");
//         }, 1);
//     });
//     function stubRequest(status = 200,url = '/api/admin/reportCracking'){
//
//         mockAxios.onGet(url).reply(status,fakeRequestData)
//
//     }
// })
//

import { mount } from '@vue/test-utils';

import CrackingReport from "../../../../../resources/js/Pages/Report/ViewCrackingReports.vue";

import {createStore} from "vuex";

describe('CrackingReport', () => {

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

        wrapper = mount(CrackingReport,{

            global : { plugins : [store], stubs:['custom-loader', 'alert', 'data-table'] },

        })
    })

    it('data-table should exists when page created', async () => {

        await expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("return row->created_at for `created_at` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.license_code('test', {'license_code': '201210124567'})).toEqual("201210124567")
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
                        {product_id : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "product_id", "product_id":3}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

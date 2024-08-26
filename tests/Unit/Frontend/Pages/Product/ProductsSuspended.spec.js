import { mount } from '@vue/test-utils';

import ProductsIndex from "../../../../../resources/js/Pages/Product/ProductsIndex.vue";

import {createStore} from "vuex";

describe('ProductsIndex', () => {

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

        wrapper = mount(ProductsIndex,{

            global : { plugins : [store], stubs:['custom-loader', 'alert', 'data-table'] },

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

        expect(wrapper.vm.options.templates.total_licenses('test', {'total_licenses': 0})).toEqual("---")
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
            "sort_order": "desc",
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

        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "product_id", "view_url": "/products/3/view", "product_id":3, "tooltip":"suspend", "modalMessage": "are_you_sure_to_suspend","btnTitle":"suspend", "modalTitle":"suspend","edit_url": "/products/3/edit", "delete_url": "/api/admin/allProductDelete"}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

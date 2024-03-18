import { mount } from '@vue/test-utils';

import VersionsView from "../../../../../resources/js/Pages/Version/VersionsView.vue";

import {createStore} from "vuex";

describe('VersionsView', () => {

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

        wrapper = mount(VersionsView,{

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

    it("return row->callback_date_time for `callback_date_time` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.callback_date_time('test', {})).toEqual("----")
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

    it("responseAdapter set edit_url, delete_url and view_url to the data property", () => {

        let responseAdpData = {
            "data": {
                "data": {
                    "data": [
                        {version_id : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "version_id" , "version_id":3}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

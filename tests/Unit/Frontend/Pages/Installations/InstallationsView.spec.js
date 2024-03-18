import { mount } from '@vue/test-utils';

import InstallationsView from "../../../../../resources/js/Pages/Installations/InstallationsView.vue";

import {createStore} from "vuex";

describe('InstallationsView', () => {

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

        wrapper = mount(InstallationsView,{

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

    it("return row->callback_ip for `ip_address` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.callback_ip('test', {'callback_ip': '2012.1012.4567'})).toEqual("2012.1012.4567")
    })

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
                        {installation_id : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "installation_id" , "installation_id":3}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

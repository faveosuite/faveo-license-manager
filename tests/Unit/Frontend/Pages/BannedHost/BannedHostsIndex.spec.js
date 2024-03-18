import { mount } from '@vue/test-utils';

import BannedHostsIndex from "../../../../../resources/js/Pages/BannedHost/BannedHostsIndex.vue";

import {createStore} from "vuex";

describe('BannedHostsIndex', () => {

    let wrapper;

    let store;

    let actions;

    let getters;

    const emitter = {
        on: jest.fn(),
    };

    getters = {

        formattedTime: () => () => {return ''}
    }

    actions = { unsetValidationError: jest.fn() }

    store = createStore({ getters, actions })

    beforeEach(()=>{

        wrapper = mount(BannedHostsIndex,{

            global : {
                plugins : [store],
                mocks: { emitter },
                stubs:['custom-loader', 'alert', 'data-table']
            },
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

        expect(wrapper.vm.options.templates.banned_host_ip('test', {'banned_host_ip': 'test'})).toEqual("test")
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
                        {banned_host_id : 1, banned_host_comments : 3},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 1,  "keyVal" : "banned_host_id", "banned_host_comments": 3, "banned_host_id":1, "edit_url": "/banned-hosts/1/edit", "delete_url": "/api/admin/bannedHosts/delete"}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})







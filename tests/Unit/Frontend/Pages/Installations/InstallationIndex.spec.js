import { mount } from '@vue/test-utils';

import InstallationsIndex from "../../../../../resources/js/Pages/Installations/InstallationsIndex.vue";

import {createStore} from "vuex";

describe('InstallationsIndex', () => {

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

        wrapper = mount(InstallationsIndex,{

            global : { plugins : [store], mocks: { emitter }, stubs:['custom-loader', 'alert', 'data-table'] },

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
                        {installation_id : 3},
                    ],
                    "total": 1
                }
            }
        }

        let responseAdpDataReturn = {"count": 1, "data": [{ "idVal" : 3, "keyVal" : "installation_id", "view_url": "/installations/3/view", "installation_id":3, "edit_url": "/installations/3/edit", "delete_url": "/api/admin/installations/delete"}]}


        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

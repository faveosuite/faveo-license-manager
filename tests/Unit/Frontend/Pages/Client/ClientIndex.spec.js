import { mount } from '@vue/test-utils';
import { formatDateTime } from '../../../../../resources/js/helpers/extraLogics'
import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
import {createStore} from "vuex";

jest.mock('../../../../../resources/js/helpers/extraLogics', ()=>({
    formatDateTime: jest.fn(),
    lang: jest.fn()
}));

describe('ClientsIndex', () => {

    let wrapper;

    let store;

    let actions;

    let getters;

    const emitter = {
        on: jest.fn(),
    };

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

            global : { plugins : [store], stubs:['data-table'], mocks: { emitter }, },

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

    it("requestAdapter method should return `sort_field`, `sort_order`, `search_query` & `limit`", () => {
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

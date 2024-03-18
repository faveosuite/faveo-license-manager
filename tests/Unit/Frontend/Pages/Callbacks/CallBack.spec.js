import { mount } from '@vue/test-utils';

import CallbacksIndex from "../../../../../resources/js/Pages/Callbacks/CallbacksIndex.vue";


jest.mock('../../../../../resources/js/helpers/extraLogics', ()=>({
    formatDateTime: jest.fn(),
    lang: jest.fn()
}));
describe('CallbacksIndex', () => {

    let wrapper;

    const emitter = {
        on: jest.fn(),
    };

    beforeEach(()=>{

        wrapper = mount(CallbacksIndex,{

            global : { stubs:['custom-loader', 'alert', 'data-table'], mocks: { emitter }, },

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
            "perPage": 10
        }
        expect(wrapper.vm.options.requestAdapter(reqAdptData)).toEqual(reqAdptDataReturn)
    });

    it("`responseAdapter` should return all data", () => {

        let responseAdpData = {
            "data": {
                "data": {
                    "data": [
                        {callback_id: 1,subject:'name'},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count" : 1, "data": [{callback_id: 1, idVal: 1, keyVal: "callback_id" ,subject:'name'}]};

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });
})

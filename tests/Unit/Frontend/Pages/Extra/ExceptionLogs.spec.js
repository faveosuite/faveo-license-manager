import { mount } from '@vue/test-utils';

import ExceptionLogs from "../../../../../resources/js/Pages/Extra/ExceptionLogs.vue";

describe('ExceptionLogs', () => {

    let wrapper;

    beforeEach(()=>{

        wrapper = mount(ExceptionLogs,{

            global : { stubs:['data-table', 'custom-loader', 'alert'] },

            props : {generalSetting : {
                time_format : {js_format:81},
                timezone : {name : 'hello'},
                date_format : {js_format : 8765}
            }}
        })
    })

    it('data-table should exists when page created', async () => {

        await expect(wrapper.find('data-table-stub').exists()).toBe(true)
    });

    it("return row->category for `category` column in template option of datatable", () => {

        expect(wrapper.vm.options.templates.category('test', {'category' : {'name' : 'default'}})).toEqual("default")
    })

    it("requestAdapter method should return `sort_field`, `sort_order`, `search_query` & `perPage`", () => {
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

    it("`responseAdapter` set message field to the data property", () => {

        let responseAdpData = {
            "data": {
                "data": {
                    "data": [
                        {message: "hello world"},
                    ],
                    "total": 1
                }
            }
        }
        let responseAdpDataReturn = {"count": 1, "data": [{ message: "hello world"}]}

        expect(wrapper.vm.options.responseAdapter(responseAdpData)).toEqual(responseAdpDataReturn)
    });

})

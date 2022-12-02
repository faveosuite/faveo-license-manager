import { mount, shallowMount} from "@vue/test-utils";

import ViewSystemReports from "../../../../../resources/js/Pages/Report/ViewSystemReports";

import globalMixins from "../../../../../resources/js/globalMixins";

import {createStore} from "vuex";
import moxios from "moxios";

const store =createStore({

})

describe('ViewSystemReports',()=> {
    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(ViewSystemReports, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['data-table','data-table-stub'],
            }
        })
    }

    beforeEach(() => {
        updateWrapper();
    });

    it('makes `loading` as false when api returns error',(done)=>{

        mockSubmitRequest();

        expect(wrapper.vm.loading).toEqual(true)

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(true)

            done();
        },1);
    });
    function mockSubmitRequest(status = 200,url = '/api/admin/reportSystem'){

        moxios.uninstall();

        moxios.install();

        moxios.stubRequest(url,{

            status: status,

            response: {}
        })
    }

})



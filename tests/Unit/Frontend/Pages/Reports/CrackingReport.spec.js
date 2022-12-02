import { mount, shallowMount} from "@vue/test-utils";

import ViewCrackingReports from "../../../../../resources/js/Pages/Report/ViewCrackingReports";

import globalMixins from "../../../../../resources/js/globalMixins";

import {createStore} from "vuex";
import moxios from "moxios";

const store =createStore({

})

describe('ViewCrackingReports',()=> {
    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(ViewCrackingReports, {
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
    function mockSubmitRequest(status = 200,url = 'api/admin/reportCracking'){

        moxios.uninstall();

        moxios.install();

        moxios.stubRequest(url,{

            status: status,

            response: {}
        })
    }

})



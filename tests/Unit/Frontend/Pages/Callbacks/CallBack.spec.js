import { mount, shallowMount} from "@vue/test-utils";

import callbacksIndex from "../../../../../resources/js/Pages/Callbacks/CallbacksIndex";

import globalMixins from "../../../../../resources/js/globalMixins";

import {createStore} from "vuex";

import moxios from 'moxios';

const store =createStore({

})

describe('CallbacksIndex',()=> {
    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(callbacksIndex, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['v-client-table','data-table-stub'],
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
    function mockSubmitRequest(status = 200,url = 'api/admin/showLicenseCallbacks'){

        moxios.uninstall();

        moxios.install();

        moxios.stubRequest(url,{

            status: status,

            response: {}
        })
    }


})



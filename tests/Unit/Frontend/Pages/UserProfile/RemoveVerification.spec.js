import { mount } from '@vue/test-utils'

import RemoveVerification from '../../../../../resources/js/Pages/UserProfile/RemoveVerification.vue'

import MockAdapter from "axios-mock-adapter";
import axios from "axios";
let axiosMock;

describe('RemoveVerification',() => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(RemoveVerification,{

            props : {

                showModal : true,

                id : 1,

                onClose : jest.fn()
            }
        })
    }

    beforeEach(() => {

        updateWrapper();

        axiosMock = new MockAdapter(axios);
    })

    afterEach(() => {

        if(axiosMock) { axiosMock.restore();}
    })

    it("removes `Two factor Auth` when `onRemove` method called",(done)=>{

        removeRequest();

        wrapper.vm.onRemove();

        expect(wrapper.vm.loading).toEqual(true);

        expect(wrapper.vm.isDisabled).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            expect(wrapper.vm.isDisabled).toEqual(false);

            expect(axiosMock.history.post[0].url).toBe('/api/admin/2fa/disable');

            expect(wrapper.vm.onClose).toHaveBeenCalled();

            done();
        },1)
    });

    it("makes `loading` as false when `onRemove` method returns error",(done)=>{

        removeRequest(400);

        wrapper.vm.onRemove();

        expect(wrapper.vm.loading).toEqual(true);

        expect(wrapper.vm.isDisabled).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            expect(wrapper.vm.isDisabled).toEqual(false);

            expect(axiosMock.history.post[0].url).toBe('/api/admin/2fa/disable');

            expect(wrapper.vm.onClose).toHaveBeenCalled();

            done();
        },1)
    });

    function removeRequest(status = 200,url = '/api/admin/2fa/disable'){

        axiosMock.onPost(url).reply(status,{})
    }
})

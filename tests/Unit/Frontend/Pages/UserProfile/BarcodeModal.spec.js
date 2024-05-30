import { mount } from '@vue/test-utils'

import {createStore} from "vuex";

import BarcodeModal from '../../../../../resources/js/Pages/UserProfile/BarcodeModal.vue'

import MockAdapter from "axios-mock-adapter";
import axios from "axios";
let axiosMock;

describe('BarcodeModal',() => {

    let wrapper;

    let actions

    let store

    actions = {

        unsetValidationError: jest.fn(),

        setValidationError : jest.fn()
    }

    store = createStore({ actions })

    const updateWrapper = () =>{

        wrapper = mount(BarcodeModal,{

            global : {
                plugins : [store],
                stubs: ['text-field'],
            },

            props : {
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

    it("makes an API call when `getBarCode` method called",(done)=>{

        dataRequest();

        wrapper.vm.getBarCode();

        expect(wrapper.vm.loading).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            expect(axiosMock.history.post[0].url).toBe('/api/admin/2fa/enable')

            done();
        },1)
    });

    it("makes `factorData` value as empty when API call returns error",(done)=>{

        dataRequest(400);

        wrapper.vm.getBarCode();

        expect(wrapper.vm.loading).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            expect(wrapper.vm.factorData).toEqual('');

            expect(axiosMock.history.post[0].url).toBe('/api/admin/2fa/enable')

            done();
        },1)
    });

    it('updates `password` value when onChange method is called',()=>{

        wrapper.vm.onChange('123456', 'password');

        expect(wrapper.vm.password).toEqual('123456');
    });

    it("validates the `password` when `validatePass` method called",(done)=>{

        validateRequest();

        wrapper.vm.validatePass();

        wrapper.setData({ password : 'password'});

        expect(wrapper.vm.verifyLoader).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.verifyLoader).toEqual(false);

            expect(wrapper.vm.passwordVerified).toEqual(true);

            done();
        },1)
    });

    it("calls `setValidationError` method when `validatePass` method returns error",(done)=>{

        validateRequest(400);

        wrapper.vm.validatePass();

        wrapper.setData({ password : 'password'});

        expect(wrapper.vm.verifyLoader).toEqual(true);

        setTimeout(()=>{

            expect(wrapper.vm.verifyLoader).toEqual(false);

            expect(wrapper.vm.passwordVerified).toEqual(false);

            expect(axiosMock.history.post[0].url).toBe('/api/admin/verify/password')

            expect(actions.setValidationError).toHaveBeenCalled();

            done();
        },1)
    });

    it("updates `showBarcode value as false & showPasscode value as true` when `passCode` method called",()=>{

        wrapper.vm.passCode();

        expect(wrapper.vm.showBarcode).toEqual(false);

        expect(wrapper.vm.showPasscode).toEqual(true);
    });

    it("updates `showBarcode value as true & showPasscode value as false` when `barCode` method called",()=>{

        wrapper.vm.barCode();

        expect(wrapper.vm.showBarcode).toEqual(true);

        expect(wrapper.vm.showPasscode).toEqual(false);
    });

    it("updates `showKeycode value as true & showBarcode and showPasscode value as false` when `keyCode` method called",()=>{

        wrapper.vm.keyCode();

        expect(wrapper.vm.showKeycode).toEqual(true);

        expect(wrapper.vm.showBarcode).toEqual(false);

        expect(wrapper.vm.showPasscode).toEqual(false);
    });

    it("updates `showBarcode value as true & showKeycode and showPasscode value as false` when `keyCodeReverse` method called",()=>{

        wrapper.vm.keyCodeReverse();

        expect(wrapper.vm.showKeycode).toEqual(false);

        expect(wrapper.vm.showBarcode).toEqual(true);

        expect(wrapper.vm.showPasscode).toEqual(false);
    });

    it("verifies the `pass_code` when `validatePassCode` method called",(done)=>{

        verifyRequest();

        wrapper.vm.validatePassCode();

        wrapper.setData({ pass_code : '546231'});

        expect(wrapper.vm.verifyLoader).toEqual(false);

        setTimeout(()=>{

            expect(wrapper.vm.verifyLoader).toEqual(false);

            expect(wrapper.vm.codeVerified).toEqual(false);

            // expect(axiosMock.history.type[0].url).toBe('/2fa/setupValidate')

            done();
        },1)
    });

    it("calls `setValidationError` method when `validatePassCode` method returns error",(done)=>{

        verifyRequest(400);

        wrapper.vm.validatePassCode();

        wrapper.setData({ pass_code : '546231'});

        expect(wrapper.vm.verifyLoader).toEqual(false);

        setTimeout(()=>{

            expect(wrapper.vm.verifyLoader).toEqual(false);

            expect(actions.setValidationError).toHaveBeenCalled();

            done();
        },1)
    });

    it("calls `onClose` method when `onDone` method called",async ()=>{

        await wrapper.setProps({ onClose : jest.fn()});

        await wrapper.vm.onDone();

        await expect(wrapper.vm.onClose).toHaveBeenCalled();
    });


    it("updates `showPasswordVerify` value when `getRequiredPass` method returns `password_confirmation_required`",(done)=>{

        expect(wrapper.vm.showPasswordVerify).toEqual(false);

        passRequest(400);

        wrapper.vm.getRequiredPass();

        setTimeout(()=>{

            expect(wrapper.vm.showPasswordVerify).toEqual(true);

            done();
        },1)
    });

    function passRequest(status = 200,url = '/show/verify-password'){

        axiosMock.onGet(url).reply(status,{data : 'password_confirmation_required'})
    }

    function dataRequest(status = 200,url = '/2fa/enable'){

        axiosMock.onPost(url).reply(status,{data : {
                secret : 'sdfgh123456vbnjk',
                image : 'image'
            }})
    }

    function validateRequest(status = 200,url = '/api/admin/verify/password'){

        axiosMock.onPost(url).reply(status,{})
    }

    function verifyRequest(status = 200,url = '/api/admin/2fa/setupValidate'){

        axiosMock.onPost(url).reply(status,{})
    }
})

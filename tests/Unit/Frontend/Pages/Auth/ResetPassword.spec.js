import { mount, shallowMount } from '@vue/test-utils'

import ResetPassword from "../../../../../resources/js/Pages/Auth/ResetPassword";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/resetRules";

import { createStore } from 'vuex'

import axios from "axios";

import MockAdapter from "axios-mock-adapter";

let axiosMock;

jest.mock('../../../../../resources/js/helpers/responseHandler');

const store = createStore({

    getters() {

        return {

            getUserToken: () => { return '' }
        }
    }
})

describe("ResetPassword",()=>{

    let wrapper;

    const updateWrapper = () => {

        wrapper = shallowMount(ResetPassword,{

            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['text-field','loader','alert'],
                mocks: { axios }
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();

        axiosMock = new MockAdapter(axios);
    });

    afterEach(() => {

        axiosMock.restore();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('test@gmail.com','email');

        expect(wrapper.vm.email).toEqual('test@gmail.com');
    });

    it('isValid - should return false ', done => {

        validation.validateResetSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateResetSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('makes an `post` API call when `onSubmit` method called',(done) => {

        wrapper.setData({ password : 'Password@1', password_confirmation : 'Password@1'})

        submitRequest();

        wrapper.vm.onSubmit();

        setTimeout(()=>{

            expect(axiosMock.history.post.length).toEqual(1);

            expect(axiosMock.history.post[0].data).toEqual('{\"token\":\"\",\"password\":\"Password@1\",\"email\":\"\",\"password_confirmation\":\"Password@1\"}');

            expect(axiosMock.history.post[0].url).toEqual('api/reset');

            expect(wrapper.vm.loading).toEqual(false)

            done();
        },1);
    });

    it('makes loading value as `false` when `onSubmit` method returns error',(done) => {

        wrapper.setData({ password : 'Password@1', password_confirmation : 'Password@1'})

        submitRequest(400);

        wrapper.vm.onSubmit();

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false)

            done();
        },1);
    });

    function submitRequest(status = 200,url = 'api/reset'){

        const fakeData = {};

        axiosMock.onPost(url).reply(status,fakeData);
    }
});

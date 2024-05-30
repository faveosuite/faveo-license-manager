import { mount, shallowMount } from '@vue/test-utils'

import Verify2FA from "../../../../../resources/js/Pages/Auth/Verify2FA.vue";

import globalMixins from "../../../../../resources/js/globalMixins";

import { createStore } from 'vuex'

import axios from "axios";

import MockAdapter from "axios-mock-adapter";

let axiosMock;

jest.mock('../../../../../resources/js/helpers/responseHandler');

const store = createStore({

    getters() {

        return {

            getUserToken: () => { return '1234567' }
        }
    },

    actions: {

        unsetAlert: jest.fn(),

        unsetValidationError: jest.fn(),

        setLoggedInUserToken: jest.fn(),

        setUserInfo: jest.fn(),
    }
})

const mockRouter = {
    push: jest.fn()
}

describe('Verify2FA', () => {

    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(Verify2FA, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['text-field','loader','alert','router-link'],
                mocks: { axios, $router: mockRouter, $route: {query: ''} }
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

        wrapper.vm.onChange('test_name','user_name');

        expect(wrapper.vm.user_name).toEqual('test_name');
    });

    it('makes an `post` API call when `onSubmit` method called',(done) => {

        wrapper.setData({ totp : 123456, PPAuth : 'ppauth1234@1'})

        submitRequest();

        wrapper.vm.onSubmit();

        setTimeout(async ()=>{


            expect(axiosMock.history.post[0].url).toEqual('/api/verify2fa');

            expect(mockRouter.push).toHaveBeenCalledWith('/login');


            done();
        },1);
    });

    it('makes loading value as `false` when `onSubmit` method returns error',(done) => {

        wrapper.setData({ otp : 1234, p_auth : '1234@ppauth1'})

        submitRequest(400);

        wrapper.vm.onSubmit();

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            done();
        },1);
    });

    function submitRequest(status = 200,url = '/api/verify2fa'){

        axiosMock.onPost(url).reply(status,{data: {message: 'hello world'}});
    }
})

import { mount, shallowMount } from '@vue/test-utils'

import ForgotPassword from "../../../../../resources/js/Pages/Auth/ForgotPassword";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/forgotRules";

import { createStore } from 'vuex'

import axios from "axios";

import MockAdapter from "axios-mock-adapter";

let axiosMock;

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../env', () => ({
    env: {
        VITE_RECAPTCHA_SITE_KEY: 'test-site-key',
    },
}));

window.axios = axios;
axios.defaults.baseURL = 'http://localhost';

const store = createStore({

    getters() {

        return {

            getUserToken: () => { return '' }
        }
    },

    actions: {

        unsetAlert: jest.fn(),

        unsetValidationError: jest.fn()
    }
})

const mockRouter = {
    push: jest.fn()
}

describe('ForgotPassword', () => {

    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(ForgotPassword, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['text-field','loader','alert','router-link'],
                mocks: { axios, $router: mockRouter }
            },
            props : {generalSetting : {
                    time_format : {js_format:81},
                    timezone : {name : 'Asia/Kolkata'},
                    date_format : {js_format : 8765},
                    client_logo: ''
                }}
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

        validation.validateForgotSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateForgotSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('makes an `post` API call when `onSubmit` method called',(done) => {

        wrapper.setData({ email : 'test@gmail.com' })

        submitRequest();

        wrapper.vm.onSubmit();

        setTimeout(()=>{

            expect(axiosMock.history.post[0].url).toEqual('/api/forgot');

            expect(wrapper.vm.loading).toEqual(false)

            setTimeout(()=>{

                expect(mockRouter.push).toHaveBeenCalledWith('/login');

                done();
            },4001);
        },1);
    });

    function submitRequest(status = 200,url = '/api/forgot'){

        const fakeData = {};

        axiosMock.onPost(url).reply(status,fakeData);
    }
})

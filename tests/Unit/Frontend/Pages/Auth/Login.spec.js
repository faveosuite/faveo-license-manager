import { mount, shallowMount } from '@vue/test-utils'

import Login from "../../../../../resources/js/Pages/Auth/Login";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/loginRules";

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

        setAlert: jest.fn(),

        unsetValidationError: jest.fn(),

        setLoggedInUserToken: jest.fn(),

        setUserInfo: jest.fn(),
    }
})

const mockRouter = {
    push: jest.fn()
}

describe('Login', () => {

    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(Login, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['text-field', 'loader', 'alert', 'router-link'],
                mocks: {axios, $router: mockRouter, $route: {query: 'your-query'}}
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

        wrapper.vm.onChange('test_name','user_name');

        expect(wrapper.vm.user_name).toEqual('test_name');
    });

    it('isValid - should return false ', done => {

        validation.validateLoginSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateLoginSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('makes an `post` API call when `onSubmit` method called',(done) => {

        wrapper.setData({ user_name : 'name', password : 'Password@1'})

        submitRequest();

        wrapper.vm.onSubmit();

        setTimeout(async ()=>{


            expect(axiosMock.history.post[0].url).toEqual('/api/login');

            expect(mockRouter.push).toHaveBeenCalledWith('/login');


            done();
        },1);
    });

    it('makes loading value as `false` when `onSubmit` method returns error',(done) => {

        wrapper.setData({ user_name : 'name', password : 'Password@1'})

        submitRequest(400);

        wrapper.vm.onSubmit();

        setTimeout(()=>{

            expect(wrapper.vm.loading).toEqual(false);

            expect(mockRouter.push).not.toHaveBeenCalledWith('/dashboard')

            done();
        },1);
    });

    function submitRequest(status = 200,url = '/api/login'){

        const fakeData = { data : { token : 'ddd', user : { id :1 , name : 'name'}}};

        axiosMock.onPost(url).reply(status,fakeData);
    }
})

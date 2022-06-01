import { shallow, createLocalVue, mount } from '@vue/test-utils'
import Vue from 'vue'
import Vuex from 'vuex'
import moxios from 'moxios';
import Login from '../../../../resources/js/components/Auth/Login.vue'
import VueRouter from 'vue-router'

jest.mock('helpers/responseHandler')

jest.mock("helpers/validator/loginRules");

const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex);
const router = new VueRouter()

describe('Login', () => {
    let wrapper

    let store;

    beforeEach(() => {

        let getters;

        getters = {

            getUserToken: () => { return '' }
        }

        store = new Vuex.Store({ getters })

        jest.spyOn(console, 'error').mockImplementation(() => { });

    })

    const updateWrapper = () => {
        wrapper = mount(Login, {
            stubs: ['text-field'],
            mocks: {
                lang: (string) => string
            },
            propsData: {
                layout: {
                    'language': 'en',
                },
            },
            localVue,
            router,
            store
        })
    }

    it('is vue instance', () => {
        updateWrapper()
        expect(wrapper.isVueInstance()).toBeTruthy()
    });

    it('updates `loading` value correctly when `onSubmit`method called', (done) => {
        updateWrapper()
        mockLogin();
        wrapper.vm.isValid = () => { return true }
        wrapper.vm.onSubmit();

        setTimeout(() => {
            expect(wrapper.vm.$data.loading).toBe(false)
            done();
        }, 1);

    });

    it('makes a post api call as soon as `onSubmit` is called', (done) => {
        updateWrapper();
        mockLogin();
        wrapper.vm.isValid = () => { return true }
        wrapper.vm.onSubmit()

        setTimeout(() => {
            expect(moxios.requests.mostRecent().url).toBe('/api/login');
            expect(moxios.requests.mostRecent().config.method).toBe('post');
            done();
        }, 1)
    })

    function mockLogin(status = 200, url = '/api/login') {
        moxios.uninstall();
        moxios.install();
        moxios.stubRequest(url, {
            status: status,
            response: {
                'success': true,
            }
        })
    };
})
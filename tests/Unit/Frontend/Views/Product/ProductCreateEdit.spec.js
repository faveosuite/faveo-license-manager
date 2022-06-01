import { shallow, createLocalVue, mount } from '@vue/test-utils'
import Vue from 'vue'
import Vuex from 'vuex'
import moxios from 'moxios';
import ProductCreateEdit from '../../../../../resources/js/components/Views/Product/ProductCreateEdit.vue'
import VueRouter from 'vue-router'

jest.mock('helpers/responseHandler')

jest.mock("helpers/extraLogics");



const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex);
const router = new VueRouter()

describe('ProductCreateEdit', () => {
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
        wrapper = mount(ProductCreateEdit, {
            stubs: ['text-field', 'number-field', 'radio-options'],
            mocks: {
                lang: (string) => string
            },
            propsData: {
                layout: {
                    'language': 'en',
                },
            },
            localVue,
            router, store
        })
    }

    it('is vue instance', () => {
        updateWrapper()
        expect(wrapper.isVueInstance()).toBeTruthy()
    });

    it('updates `loading` value correctly when `onSubmit`method called', (done) => {
        updateWrapper()

        mockProductCreateEdit();

        wrapper.vm.onSubmit();

        setTimeout(() => {
            expect(wrapper.vm.$data.loading).toBe(false)
            done();
        }, 1);

    });

    it('makes a post api call as soon as `onSubmit` is called', (done) => {
        updateWrapper();
        mockProductCreateEdit();
        wrapper.vm.onSubmit()

        setTimeout(() => {
            expect(moxios.requests.mostRecent().url).toBe('/api/admin/products/add');
            expect(moxios.requests.mostRecent().config.method).toBe('post');
            done();
        }, 1)
    })

    function mockProductCreateEdit(status = 200, url = '/api/admin/products/add') {
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
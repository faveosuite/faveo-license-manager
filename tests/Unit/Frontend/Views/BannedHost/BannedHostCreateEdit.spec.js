import { shallow, createLocalVue, mount } from '@vue/test-utils'
import Vue from 'vue'
import Vuex from 'vuex'
import moxios from 'moxios';
import BannedHostCreateEdit from '../../../../../resources/js/components/Views/BannedHost/BannedHostCreateEdit.vue'
import VueRouter from 'vue-router'

jest.mock('helpers/responseHandler')

jest.mock("helpers/extraLogics");



const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex);
const router = new VueRouter()

describe('BannedHostCreateEdit', () => {
    let wrapper

    let store;

    beforeEach(() => {

        moxios.install();

        let getters;

        getters = {

            getUserToken: () => { return '' },
            getApiKey: () => { return 'apiKey' }
        }

        store = new Vuex.Store({ getters })

        jest.spyOn(console, 'error').mockImplementation(() => { });

    })

    afterEach(() => {
        moxios.uninstall();
    });

    const updateWrapper = () => {
        wrapper = mount(BannedHostCreateEdit, {
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
        mockBannedHostCreateEdit();
        wrapper.vm.onSubmit();

        setTimeout(() => {
            expect(wrapper.vm.$data.loading).toBe(false)
            done();
        }, 1);

    });

    it('makes a post api call as soon as `onSubmit` is called', (done) => {
        updateWrapper();
        mockBannedHostCreateEdit();
        wrapper.setData({ ipAddress: 'ip', comments: 'comment' })
        wrapper.vm.onSubmit()

        setTimeout(() => {
            expect(moxios.requests.mostRecent().url).toBe('/api/admin/bannedHosts/add');
            expect(moxios.requests.mostRecent().config.method).toBe('post');
            done();
        }, 1)
    })

    function mockBannedHostCreateEdit(status = 200, url = '/api/admin/bannedHosts/add') {
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
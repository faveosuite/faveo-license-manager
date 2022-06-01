import { shallow, createLocalVue, mount } from '@vue/test-utils'
import Vue from 'vue'
import Vuex from 'vuex'
import moxios from 'moxios';
import CustomizeNotifications from '../../../../../resources/js/components/Views/ServerNotifications/CustomizeNotifications.vue'
import VueRouter from 'vue-router'

jest.mock('helpers/responseHandler')

jest.mock("helpers/extraLogics");



const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex);
const router = new VueRouter()

describe('CustomizeNotifications', () => {
  let wrapper

  let store;

  beforeEach(() => {
    let getters;

    getters = {

      getApiKey: () => { return '' }
    }

    store = new Vuex.Store({ getters })

    jest.spyOn(console, 'error').mockImplementation(() => { });

  })

  const updateWrapper = () => {
    wrapper = mount(CustomizeNotifications, {
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
    mockCustomizeNotifications();
    wrapper.vm.onSubmit();

    setTimeout(() => {
      expect(wrapper.vm.$data.loading).toBe(true)
      done();
    }, 1);

  });

  it('makes a post api call as soon as `onSubmit` is called', (done) => {
    updateWrapper();
    mockCustomizeNotifications();
    wrapper.vm.onSubmit()

    setTimeout(() => {
      expect(moxios.requests.mostRecent().url).toBe('/api/admin/notifications/');
      expect(moxios.requests.mostRecent().config.method).toBe('post');
      done();
    }, 1)
  })

  function mockCustomizeNotifications(status = 200, url = '/api/admin/viewNotifications') {
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
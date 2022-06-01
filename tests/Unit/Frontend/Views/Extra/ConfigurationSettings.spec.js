import { shallow, createLocalVue, mount } from '@vue/test-utils'
import Vue from 'vue'
import Vuex from 'vuex'
import moxios from 'moxios';
import ConfigurationSettings from '../../../../../resources/js/components/Views/Extra/ConfigurationSettings.vue'
import VueRouter from 'vue-router'

jest.mock('helpers/responseHandler')

jest.mock("helpers/extraLogics");



const localVue = createLocalVue()
localVue.use(VueRouter)
localVue.use(Vuex);
const router = new VueRouter()

describe('ConfigurationSettings', () => {
  let wrapper

  beforeEach(() => {

    jest.spyOn(console, 'error').mockImplementation(() => { });

  })

  const updateWrapper = () => {
    wrapper = mount(ConfigurationSettings, {
      stubs: ['text-field', 'dynamic-select'],
      mocks: {
        lang: (string) => string
      },
      propsData: {
        layout: {
          'language': 'en',
        },
      },
      localVue,
      router
    })
  }

  it('is vue instance', () => {
    updateWrapper()
    expect(wrapper.isVueInstance()).toBeTruthy()
  });

  it('updates `loading` value correctly when `onSubmit`method called', (done) => {
    updateWrapper()

    mockConfigurationSettings();

    wrapper.vm.onSubmit();

    setTimeout(() => {
      expect(wrapper.vm.$data.loading).toBe(false)
      done();
    }, 1);

  });

  it('makes a post api call as soon as `onSubmit` is called', (done) => {
    updateWrapper();
    mockConfigurationSettings();
    wrapper.vm.onSubmit()

    setTimeout(() => {
      expect(moxios.requests.mostRecent().url).toBe('/api/admin/config');
      expect(moxios.requests.mostRecent().config.method).toBe('post');
      done();
    }, 1)
  })

  function mockConfigurationSettings(status = 200, url = '/api/admin/config') {
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
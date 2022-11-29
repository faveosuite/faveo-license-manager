import { mount, shallowMount } from '@vue/test-utils'

import SecuritySettings from '../../../../../resources/js/Pages/Settings/SecuritySettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateSecuritySettings";

import {createStore} from "vuex";

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

const store = createStore({

    getters() {

        return {

            getApiKey: () => { return '' },

            getUserToken: () => { return '' }
        }
    },

    actions: {

        fetchSettings: jest.fn()
    }
})

describe('SecuritySettings', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(SecuritySettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','number-field','static-select','dynamic-select','radio-button']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('test','WHITELISTED_IP');

        expect(wrapper.vm.whiteListedIp).toEqual('test');
    });

    it('isValid - should return false ', done => {

        validation.validateSecuritySettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateSecuritySettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

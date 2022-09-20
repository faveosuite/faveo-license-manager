import { mount, shallowMount } from '@vue/test-utils'

import ConfigurationSettings from '../../../../../resources/js/Pages/Extra/ConfigurationSettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateConfigGenerator";

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
})

describe('ConfigurationSettings', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(ConfigurationSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','dynamic-select','response-modal']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('1','License_Verification_Period');

        expect(wrapper.vm.License_Verification_Period).toEqual('1');
    });

    it('isValid - should return false ', done => {

        validation.validateConfigGenerator = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateConfigGenerator = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

import { mount, shallowMount } from '@vue/test-utils'

import SystemCleanupSettings from '../../../../../resources/js/Pages/Settings/SystemCleanupSettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateCleanUpSettings";

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

describe('SystemCleanupSettings', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(SystemCleanupSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','number-field','static-select','dynamic-select']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange({id:1,name:'name'},'DATABASE_CLEANUP_ENABLED');

        expect(wrapper.vm.autoSystemCleanupType).toEqual({id:1,name:'name'});
    });

    it('isValid - should return false ', done => {

        validation.validateCleanUpSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateCleanUpSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

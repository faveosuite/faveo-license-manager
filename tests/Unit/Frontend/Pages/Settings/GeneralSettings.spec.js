import { mount, shallowMount } from '@vue/test-utils'

import GeneralSettings from "../../../../../resources/js/Pages/Settings/GeneralSettings";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateGeneralSettings";

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

describe('GeneralSettings', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(GeneralSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','dynamic-select']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange({id:1,name:'test'},'SMART_REPORTS');

        expect(wrapper.vm.smartReportsType).toEqual({id:1,name:'test'});
    });

    it('isValid - should return false ', done => {

        validation.validateGeneralSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateGeneralSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

import { mount, shallowMount } from '@vue/test-utils'

import LicenseCreateEdit from '../../../../../resources/js/Pages/License/LicenseCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateLicenseSettings";

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

describe('LicenseCreateEdit', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(LicenseCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','number-field','dynamic-select','static-select','radio-button','date-picker']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('name','client_name');

        expect(wrapper.vm.client_name).toEqual('name');
    });

    it('isValid - should return false ', done => {

        validation.validateLicenseSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateLicenseSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

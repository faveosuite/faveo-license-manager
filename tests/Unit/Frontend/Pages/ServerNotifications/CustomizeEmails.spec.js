import { mount, shallowMount } from '@vue/test-utils'

import CustomizeEmails from '../../../../../resources/js/Pages/ServerNotifications/CustomizeEmails'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/customEmailValidation";

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

describe('CustomizeEmails', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(CustomizeEmails, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('subject','email_expiring_license_subject');

        expect(wrapper.vm.email_expiring_license_subject).toEqual('subject');
    });

    it('isValid - should return false ', done => {

        validation.validateCustomEmailSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateCustomEmailSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

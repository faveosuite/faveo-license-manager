import { mount, shallowMount } from '@vue/test-utils'

import CustomizeNotifications from '../../../../../resources/js/Pages/ServerNotifications/CustomizeNotifications'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/customNoteValidation";

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

describe('CustomizeNotifications', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(CustomizeNotifications, {
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

        wrapper.vm.onChange('test','notification_product_not_found');

        expect(wrapper.vm.notification_product_not_found).toEqual('test');
    });

    it('isValid - should return false ', done => {

        validation.validateCustomNoteSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateCustomNoteSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

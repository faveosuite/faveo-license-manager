import { mount, shallowMount } from '@vue/test-utils'

import BannedHostCreateEdit from '../../../../../resources/js/Pages/BannedHost/BannedHostCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/bannedHostValidation";

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

describe('BannedHostCreateEdit', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(BannedHostCreateEdit, {
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

        wrapper.vm.onChange('banned_host_ip','banned_host_ip');

        expect(wrapper.vm.banned_host_ip).toEqual('banned_host_ip');
    });

    it('isValid - should return false ', done => {

        validation.bannedHostValidation = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.bannedHostValidation = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

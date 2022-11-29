import { mount, shallowMount } from '@vue/test-utils'

import ClientCreateEdit from '../../../../../resources/js/Pages/Client/ClientCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/clientValidation";

import {createStore} from "vuex";
import {validateClientSettings} from "../../../../../resources/js/helpers/validator/clientValidation";

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

describe('ClientCreateEdit', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(ClientCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','radio-button']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('name','client_fname');

        expect(wrapper.vm.client_fname).toEqual('name');
    });

    it('isValid - should return false ', done => {

        validation.validateClientSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateClientSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

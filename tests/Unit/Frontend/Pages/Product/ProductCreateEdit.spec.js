import { mount, shallowMount } from '@vue/test-utils'

import ProductCreateEdit from '../../../../../resources/js/Pages/Product/ProductCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/productValidation";

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

describe('ProductCreateEdit', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(ProductCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','radio-button','number-field']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('title','product_title');

        expect(wrapper.vm.product_title).toEqual('title');
    });

    it('isValid - should return false ', done => {

        validation.validateProductSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateProductSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

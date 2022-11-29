import { mount, shallowMount } from '@vue/test-utils'

import ApiKeyCreateEdit from '../../../../../resources/js/Pages/APIKey/APIKeyCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/ApiKeysValidation";

import axios from "axios";

import MockAdapter from "axios-mock-adapter";

import {createStore} from "vuex";

let axiosMock;

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

const mockRouter = {
    push: jest.fn()
}

const store = createStore({

    getters() {

        return {

            getApiKey: () => { return '' }
        }
    },
})

describe('ApiKeyCreateEdit', () => {

    let wrapper;

    const updateWrapper = () => {

        wrapper = mount(ApiKeyCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field', 'number-field', 'static-select', 'dynamic-select'],
                mocks: { axios, $router: mockRouter }
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();

        axiosMock = new MockAdapter(axios);
    });

    afterEach(() => {

        if(axiosMock) {

            axiosMock.restore();
        }
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('secret','api_key_secret');

        expect(wrapper.vm.api_key_secret).toEqual('secret');
    });

    it('isValid - should return false ', done => {

        validation.ApiKeysValidation = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.ApiKeysValidation = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });
})

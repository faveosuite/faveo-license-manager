import { mount, shallowMount } from '@vue/test-utils'

import LicenseCreateEdit from '../../../../../resources/js/Pages/License/LicenseCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateLicenseSettings";

import {createStore} from "vuex";

import MockAdapter from 'axios-mock-adapter';

import axios from 'axios';

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

const store = createStore({

    getters() {

        return {

            getApiKey: () => { return '' },

            getUserToken: () => { return '' },

            getUserData: () => {  return { client_id: 2 }  }
        }
    },
})

describe('LicenseCreateEdit', () => {

    let wrapper;

    let mockAxios = new MockAdapter(axios);


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

        mockAxios = new MockAdapter(axios);

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

    it('submits form data successfully for add', async () => {
        mockAxios.onPost('/api/admin/license/add').reply(200, { data: 'success' });

        await wrapper.setData({
            product_id: { id: 1, name: 'Product 1' },
        });

        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(true);
    });
    it('handles a failed form submission', async () => {
        mockAxios.onPost('/api/admin/license/add').reply(500, { error: 'server error' });

        await wrapper.setData({
            license_code: 'your_license_code',
        });

        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(true);
    });
})

import { mount, shallowMount } from '@vue/test-utils'

import ClientCreateEdit from '../../../../../resources/js/Pages/Client/ClientCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/clientValidation";

import {createStore} from "vuex";

import MockAdapter from 'axios-mock-adapter';
import {validateClientSettings} from "../../../../../resources/js/helpers/validator/clientValidation";
import axios from "axios";

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
    let mockAxios = new MockAdapter(axios); // Use mockAxios instead of mock


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
        mockAxios = new MockAdapter(axios);

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

    it('submits the form successfully', async () => {
        // Mock a successful API response
        mockAxios.onPost('/api/admin/clients/add').reply(200, { data: {} });

        // Set some data in the component
        await wrapper.setData({
            client_fname: 'John',
            client_lname: 'Doe',
            client_email: 'john.doe@example.com',
            client_status: 1,
        });

        await wrapper.vm.onSubmit();

        expect(mockAxios.history.post.length).toBe(1);
        expect(mockAxios.history.post[0].data).toEqual(expect.stringContaining('John'));

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles API error on form submission', async () => {
        mockAxios.onPost('/api/admin/clients/add').reply(500, { error: 'Internal Server Error' });

        await wrapper.vm.onSubmit();

        expect(mockAxios.history.post.length).toBe(1);
        expect(wrapper.vm.loading).toBe(false);
    });
})

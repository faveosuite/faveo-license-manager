import { mount, shallowMount } from '@vue/test-utils'

import SystemCleanupSettings from '../../../../../resources/js/Pages/Settings/SystemCleanupSettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateCleanUpSettings";

import {createStore} from "vuex";
import MockAdapter from 'axios-mock-adapter';
import axios from 'axios';

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

describe('SystemCleanupSettings', () => {

    let wrapper;
    let mockAxios = new MockAdapter(axios);


    const updateWrapper = () => {

        wrapper = mount(SystemCleanupSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','number-field','static-select','dynamic-select']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
        mockAxios = new MockAdapter(axios);

    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange({id:1,name:'name'},'DATABASE_CLEANUP_ENABLED');

        expect(wrapper.vm.autoSystemCleanupType).toEqual({id:1,name:'name'});
    });

    it('isValid - should return false ', done => {

        validation.validateCleanUpSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateCleanUpSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('submits form data successfully', async () => {
        mockAxios.onPost('/api/admin/cleanupsettings/new').reply(200, { data: 'success' });

        await wrapper.setData({
            autoSystemCleanupType: { name: 'Enabled', value: 1 },
            removeOlderCallbacksOptions: { title: 'Option 1', value: 1 },
            removeLicenseReportsOptions: { title: 'Option 2', value: 2 },
            removeSystemReportsOptions: { title: 'Option 3', value: 3 },
            removeLicenseCancelledType: { name: 'Enabled', value: 1 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error', async () => {
        mockAxios.onPost('/api/admin/cleanupsettings/new').reply(500, { error: 'Internal Server Error' });

        await wrapper.setData({
            autoSystemCleanupType: { name: 'Enabled', value: 1 },
            removeOlderCallbacksOptions: { title: 'Option 1', value: 1 },
            removeLicenseReportsOptions: { title: 'Option 2', value: 2 },
            removeSystemReportsOptions: { title: 'Option 3', value: 3 },
            removeLicenseCancelledType: { name: 'Enabled', value: 1 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });
})

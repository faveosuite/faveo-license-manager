import { mount, shallowMount } from '@vue/test-utils'

import EmailSettings from '../../../../../resources/js/Pages/Settings/EmailSettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateEmailSettings";

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

describe('EmailSettings', () => {

    let wrapper;
    let mockAxios = new MockAdapter(axios);


    const updateWrapper = () => {

        wrapper = mount(EmailSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','number-field','static-select','dynamic-select','radio-button']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
        mockAxios = new MockAdapter(axios);

    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('test','EMAIL_FROM_NAME');

        expect(wrapper.vm.fromName).toEqual('test');
    });

    it('isValid - should return false ', done => {

        validation.validateEmailSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateEmailSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('submits form data successfully', async () => {
        mockAxios.onPost('/api/admin/emailsettings/new').reply(200, { data: 'success' });

        await wrapper.setData({
            fromName: 'John Doe',
            fromAddress: 'john.doe@example.com',
            copySenderType: { name: 'Enabled', value: 1 },
            licenseReminderType: { name: 'Enabled', value: 1 },
            updatesReminderType: { name: 'Enabled', value: 1 },
            supportReminderType: { name: 'Enabled', value: 1 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error', async () => {
        mockAxios.onPost('/api/admin/emailsettings/new').reply(500, { error: 'Internal Server Error' });

        await wrapper.setData({
            fromName: 'John Doe',
            fromAddress: 'john.doe@example.com',
            copySenderType: { name: 'Enabled', value: 1 },
            licenseReminderType: { name: 'Enabled', value: 1 },
            updatesReminderType: { name: 'Enabled', value: 1 },
            supportReminderType: { name: 'Enabled', value: 1 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });
})

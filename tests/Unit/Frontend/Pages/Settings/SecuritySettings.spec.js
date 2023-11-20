import { mount, shallowMount } from '@vue/test-utils'

import SecuritySettings from '../../../../../resources/js/Pages/Settings/SecuritySettings'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateSecuritySettings";

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

describe('SecuritySettings', () => {

    let wrapper;
    let mockAxios = new MockAdapter(axios);


    const updateWrapper = () => {

        wrapper = mount(SecuritySettings, {
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

        wrapper.vm.onChange('test','WHITELISTED_IP');

        expect(wrapper.vm.whiteListedIp).toEqual('test');
    });

    it('isValid - should return false ', done => {

        validation.validateSecuritySettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateSecuritySettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('submits form data successfully', async () => {
        mockAxios.onPost('/api/admin/securitysettings/new').reply(200, { data: 'success' });

        await wrapper.setData({
            whitelistedAccessType: { name: 'Enabled', value: 1 },
            whiteListedIp: '127.0.0.1',
            bannedHostsType: { name: 'Enabled', value: 1 },
            messageBannedHosts: 'Banned hosts message',
            autobanFailedLogin: { title: 'Option 1', value: 1 },
            autobanFailedLicensing: { title: 'Option 2', value: 2 },
            ForgetFailedAttempts: { title: 'Option 3', value: 3 },
            minPasswordLength: 8,
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error', async () => {
        mockAxios.onPost('/api/admin/securitysettings/new').reply(500, { error: 'Internal Server Error' });

        await wrapper.setData({
            whitelistedAccessType: { name: 'Enabled', value: 1 },
            whiteListedIp: '127.0.0.1',
            bannedHostsType: { name: 'Enabled', value: 1 },
            messageBannedHosts: 'Banned hosts message',
            autobanFailedLogin: { title: 'Option 1', value: 1 },
            autobanFailedLicensing: { title: 'Option 2', value: 2 },
            ForgetFailedAttempts: { title: 'Option 3', value: 3 },
            minPasswordLength: 8,
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });
})

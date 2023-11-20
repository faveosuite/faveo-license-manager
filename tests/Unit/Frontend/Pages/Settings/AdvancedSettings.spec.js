import { mount, shallowMount } from '@vue/test-utils';
import AdvancedSettings from '../../../../../resources/js/Pages/Settings/AdvancedSettings';
import globalMixins from '../../../../../resources/js/globalMixins';
import * as validation from '../../../../../resources/js/helpers/validator/validateAdvancedSettings';
import { createStore } from 'vuex';
import MockAdapter from 'axios-mock-adapter';
import axios from 'axios';

jest.mock('../../../../../resources/js/helpers/responseHandler');
jest.mock('../../../../../resources/js/helpers/extraLogics');

const store = createStore({
    getters() {
        return {
            getApiKey: () => '',
            getUserToken: () => '',
        };
    },
    actions: {
        fetchSettings: jest.fn(),
    },
});

describe('AdvancedSettings', () => {
    let wrapper;
    let mockAxios = new MockAdapter(axios);

    const updateWrapper = () => {
        wrapper = mount(AdvancedSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['loader', 'custom-loader', 'alert', 'router-link', 'text-field', 'number-field', 'static-select', 'dynamic-select', 'radio-button'],
            },
        });
    };

    beforeEach(() => {
        updateWrapper();
        mockAxios = new MockAdapter(axios);
    });

    it('`onChange` - method should update the correct value to data', () => {
        wrapper.vm.onChange('test', 'ENVATO_API_TOKEN');
        expect(wrapper.vm.evantoApiToken).toEqual('test');
    });

    it('isValid - should return false', () => {
        validation.validateAdvancedSettings = jest.fn(() => ({ errors: [], isValid: false }));
        expect(wrapper.vm.isValid()).toBe(false);
    });

    it('isValid - should return true', () => {
        validation.validateAdvancedSettings = jest.fn(() => ({ errors: [], isValid: true }));
        expect(wrapper.vm.isValid()).toBe(true);
    });

    it('submits form data successfully', async () => {
        mockAxios.onPost('/api/admin/advancedsettings/new').reply(200, { data: 'success' });
        await wrapper.setData({
            autoPhpLicenserType: { name: 'Enabled', value: 1 },
            evantoApiToken: 'your_api_token',
        });
        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error ', async () => {
        mockAxios.onPost('/api/admin/advancedsettings/new').reply(500, { error: 'Internal Server Error' });
        await wrapper.setData({
            autoPhpLicenserType: { name: 'Enabled', value: 1 },
            evantoApiToken: 'your_api_token',
        });
        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(false);
    });
});

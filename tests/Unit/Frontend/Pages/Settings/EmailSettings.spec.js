import { mount } from '@vue/test-utils';
import EmailSettings from '../../../../../resources/js/Pages/Settings/EmailSettings';
import globalMixins from "../../../../../resources/js/globalMixins";
import * as validation from "../../../../../resources/js/helpers/validator/validateEmailSettings";
import { createStore } from "vuex";
import MockAdapter from 'axios-mock-adapter';
import axios from 'axios';

const store = createStore({
    getters() {
        return {
            getEmailSettings: () => { return {} },
        }
    },
    actions: {
        fetchSettings: jest.fn()
    }
});

describe('EmailSettings', () => {
    let wrapper;
    let mockAxios = new MockAdapter(axios);

    const updateWrapper = () => {
        wrapper = mount(EmailSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['loader', 'custom-loader', 'alert', 'router-link', 'text-field', 'number-field', 'static-select', 'dynamic-select', 'radio-button']
            }
        });
    }

    beforeEach(() => {
        updateWrapper();
        mockAxios = new MockAdapter(axios);
    });

    it('should render the component', () => {
        expect(wrapper.exists()).toBe(true);
    });


    it('onChange method should update correct value in data', () => {
        wrapper.vm.onChange('test', 'EMAIL_DRIVER');
        expect(wrapper.vm.emailDriver).toEqual('test');
    });

    it('isValid method should return false when validation fails', () => {
        validation.validateEmailSettings = () => ({ errors: ['Some error'], isValid: false });
        expect(wrapper.vm.isValid()).toBe(false);
    });

    it('isValid method should return true when validation passes', () => {
        validation.validateEmailSettings = () => ({ errors: [], isValid: true });
        expect(wrapper.vm.isValid()).toBe(true);
    });

    it('onSubmit method should post form data successfully', async () => {
        mockAxios.onPost('/api/admin/emailSettings').reply(200, { data: 'success' });
        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(false);
    });

    it('onSubmit method should handle submission error', async () => {
        mockAxios.onPost('/api/admin/emailSettings').reply(500, { error: 'Internal Server Error' });
        await wrapper.vm.onSubmit();
        expect(wrapper.vm.loading).toBe(false);
    });

    it('should call validateEmailSettings when checking validity', () => {
        const validateSpy = jest.spyOn(validation, 'validateEmailSettings');
        wrapper.vm.isValid();
        expect(validateSpy).toHaveBeenCalledWith(wrapper.vm.$data);
    });

});

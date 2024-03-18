import { mount, shallowMount } from '@vue/test-utils';
import VersionCreateEdit from "../../../../../resources/js/Pages/Version/VersionCreateEdit.vue";
import globalMixins from '../../../../../resources/js/globalMixins';
import * as validation from '../../../../../resources/js/helpers/validator/versionValidation'
import { createStore } from 'vuex';
import MockAdapter from 'axios-mock-adapter';
import axios from 'axios';

jest.mock('../../../../../resources/js/helpers/responseHandler');
jest.mock('../../../../../resources/js/helpers/extraLogics');
jest.mock('../../../../../resources/js/components/Reusable/FormField/DatatableDynamicSelect.vue', ()=>{})

const store = createStore({
    getters() {
        return {
            getApiKey: () => '',
            getUserToken: () => '',
        };
    },
});

describe('VersionCreateEdit', () => {
    let wrapper;
    let mockAxios = new MockAdapter(axios); // Use mockAxios instead of mock

    const updateWrapper = () => {
        wrapper = mount(VersionCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: [
                    'loader',
                    'custom-loader',
                    'alert',
                    'router-link',
                    'text-field',
                    'radio-button',
                    'number-field',
                    'date-time-picker'
                ],
            },
        });
    };

    beforeEach(() => {
        updateWrapper();
        mockAxios = new MockAdapter(axios);
    });

    it('`onChange` - method should update the correct value to data', () => {
        wrapper.vm.onChange({product_id:1, product_title:'testing'}, 'product');
        expect(wrapper.vm.product_id).toEqual(1);
    });

    it('isValid - should return false', (done) => {
        validation.validateVersionSettings = () => {
            return { errors: [], isValid: false };
        };

        expect(wrapper.vm.isValid()).toBe(false);
        done();
    });

    it('isValid - should return true', (done) => {
        validation.validateVersionSettings = () => {
            return { errors: [], isValid: true };
        };

        expect(wrapper.vm.isValid()).toBe(true);
        done();
    });

    it('submits the form successfully', async () => {
        // Mock a successful API response
        mockAxios.onPost('/api/admin/versions/add').reply(200, { data: {} });

        // Set some data in the component
        await wrapper.setData({
            product_title: 'Test Product',
            product_sku: '12345',
        });

        await wrapper.vm.onSubmit();

        expect(mockAxios.history.post.length).toBe(1);
        expect(mockAxios.history.post[0].data).toEqual(
            expect.stringContaining('Test Product')
        );

        expect(wrapper.vm.loading).toBe(true);
    });

    it('onSubmit - should handle API error response', async () => {
        mockAxios.onPost('/api/admin/versions/add').reply(500, {
            error: 'Internal Server Error',
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(true);
    });

    it('onSubmit - should handle successful API response', async () => {
        mockAxios.onPost('/api/admin/versions/add').reply(200, { data: {} });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(true);
    });

    it('onSubmit - should handle API validation error response', async () => {
        validation.validateVersionSettings = jest.fn(() => ({
            errors: ['Validation error'],
            isValid: false,
        }));
        mockAxios.onPost('/api/admin/versions/add').reply(422, {
            errors: ['Validation error'],
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
        // Add more assertions based on your component's logic for validation errors
    });
});

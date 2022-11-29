import { mount } from '@vue/test-utils';
import axios from 'axios';
import CallbacksIndex from "../../../../../resources/js/Pages/Callbacks/CallbacksIndex.vue";

jest.mock('axios');

describe('CallbacksIndex', () => {
    it('should fetch data on mount', async () => {
        axios.get.mockResolvedValue({
            data: [
                {
                    product_title: 'Product 1',
                    license_code: 'ABC123',
                    callback_ip: '127.0.0.1',
                    callback_domain: 'example.com',
                    callback_date_time: '2022-01-01 12:00:00',
                    created_at: '2022-01-01 12:00:00',
                    updated_at: '2022-01-01 12:00:00',
                },
                {
                    product_title: 'Product 2',
                    license_code: 'DEF456',
                    callback_ip: '127.0.0.2',
                    callback_domain: 'example.org',
                    callback_date_time: '2022-01-02 12:00:00',
                    created_at: '2022-01-02 12:00:00',
                    updated_at: '2022-01-02 12:00:00',
                },
            ],
        });

        const wrapper = mount(CallbacksIndex);

        await wrapper.vm.$nextTick();

        expect(axios.get).toHaveBeenCalledWith('api/admin/showLicenseCallbacks');
        expect(wrapper.vm.data).toEqual([
            {
                product_title: 'Product 1',
                license_code: 'ABC123',
                callback_ip: '127.0.0.1',
                callback_domain: 'example.com',
                callback_date_time: '2022-01-01 12:00:00',
                created_at: '2022-01-01 12:00:00',
                updated_at: '2022-01-01 12:00:00',
            },
            {
                product_title: 'Product 2',
                license_code: 'DEF456',
                callback_ip: '127.0.0.2',
                callback_domain: 'example.org',
                callback_date_time: '2022-01-02 12:00:00',
                created_at: '2022-01-02 12:00:00',
                updated_at: '2022-01-02 12:00:00',
            },
        ]);
    });
});

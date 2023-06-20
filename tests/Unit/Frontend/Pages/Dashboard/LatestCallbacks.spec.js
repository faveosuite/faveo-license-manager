import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import latestCallbacks from "../../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";

jest.mock('axios');

describe('LatestCallbacks', () => {

    it('displays fetched data correctly', async () => {
        const mockedData = [
            {
                callback_id: 1,
                license_code: 'ABC123',
                callback_ip: '192.168.0.1',
                callback_date_time: '2023-06-19 10:00:00',
                callback_domain: 'example.com',
            },
        ];

        axios.get.mockResolvedValue({ data: { data: { afu_latest_callbacks: mockedData } } });

        const wrapper = shallowMount(latestCallbacks);
        await wrapper.vm.$nextTick();

        const tableData = wrapper.vm.data;
        expect(tableData).toEqual(mockedData);
    });
    it('displays loading state during data retrieval', async () => {
        const wrapper = shallowMount(latestCallbacks);

        // Simulate data retrieval in the "beforeMount" hook
        expect(wrapper.vm.loading).toBe(true);

        // Simulate data retrieval completion
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);
    });

});

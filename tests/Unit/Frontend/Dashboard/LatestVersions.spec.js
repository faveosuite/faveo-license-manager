import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import { shallowMount } from '@vue/test-utils';
import LatestVersions from "../../../../resources/js/Pages/Dashboard/LatestVersions.vue";

describe('LatestVersions', () => {
    let mock;
    let wrapper;

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(LatestVersions);
    });

    afterEach(() => {
        mock.reset();
    });

    it('should fetch and display data', async () => {
        const responseData = {
            data: {
                latest_versions: [
                    { version: '1.0', date: '2023-06-30', expiration: '2024-06-30', status: 'Active' },
                    { version: '2.0', date: '2023-07-15', expiration: '2024-07-15', status: 'Inactive' },
                ],
            },
        };

        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(responseData.data.latest_versions);
        expect(wrapper.find('.version').text()).toBe('1.0');
        expect(wrapper.find('.date').text()).toBe('2023-06-30');
        expect(wrapper.find('.expiration').text()).toBe('2024-06-30');
        expect(wrapper.find('.status').text()).toBe('Active');
    });

    it('should handle API error', async () => {
        mock.onGet('/api/admin/dashboarddropdown').reply(500);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual([]);
    });
});

import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import { shallowMount } from '@vue/test-utils';
import LatestInstallations from "../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";

describe('LatestInstallations', () => {
    let mock;
    let wrapper;

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(LatestInstallations);
    });

    afterEach(() => {
        mock.reset();
    });

    it('should fetch and display data', async () => {
        const responseData = {
            data: {
                afl_latest_installation: [
                    { version: '1.0', ip: '192.168.0.1', date: '2023-06-30', status: 'Completed' },
                    { version: '2.0', ip: '192.168.0.2', date: '2023-07-15', status: 'In Progress' },
                ],
            },
        };

        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(responseData.data.afl_latest_installation);
        expect(wrapper.find('.version').text()).toBe('1.0');
        expect(wrapper.find('.ip').text()).toBe('192.168.0.1');
        expect(wrapper.find('.date').text()).toBe('2023-06-30');
        expect(wrapper.find('.status').text()).toBe('Completed');
    });

    it('should handle API error', async () => {
        mock.onGet('/api/admin/dashboarddropdown').reply(500);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual([]);
    });
});

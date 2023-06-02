import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import { shallowMount } from '@vue/test-utils';
import LatestProductReport from "../../../../resources/js/Pages/Dashboard/LatestProductReport.vue";

describe('LatestProductReport', () => {
    let mock;
    let wrapper;

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(LatestProductReport);
    });

    afterEach(() => {
        mock.reset();
    });

    it('should fetch and display data', async () => {
        const responseData = {
            data: {
                latest_product_reports: [
                    { report: 'Report A', date: '2023-06-30', status: 'Completed' },
                    { report: 'Report B', date: '2023-07-15', status: 'Pending' },
                ],
            },
        };

        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(responseData.data.latest_product_reports);
        expect(wrapper.find('.report').text()).toBe('Report A');
        expect(wrapper.find('.date').text()).toBe('2023-06-30');
        expect(wrapper.find('.status').text()).toBe('Completed');
    });

    it('should handle API error', async () => {
        mock.onGet('/api/admin/dashboarddropdown').reply(500);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual([]);
    });
});

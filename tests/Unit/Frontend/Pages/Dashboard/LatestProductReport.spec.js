import { shallowMount } from '@vue/test-utils';
import latestProductReport from "../../../../../resources/js/Pages/Dashboard/LatestProductReport.vue";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";

let wrapper;
describe('LatestProductReport', () => {
    it('renders without errors', () => {
        const wrapper = shallowMount(latestProductReport);
        expect(wrapper.exists()).toBe(true);
    });
    it('fetches data from API', async () => {
        const mockAxios = new MockAdapter(axios);

        const responseData = {
            data: {
                latest_product_reports: [
                    { report_id: 1, report_date_time: '2023-06-18', status: 'Pending' },
                    { report_id: 2, report_date_time: '2023-06-19', status: 'Completed' },
                ],
            },
        };
        mockAxios.onGet('/api/admin/dashboarddropdown').reply(200, responseData);
        const wrapper = shallowMount(latestProductReport);
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.data).toEqual(responseData.data.latest_product_reports);
        expect(mockAxios.history.get[0].url).toBe('/api/admin/dashboarddropdown');
        expect(mockAxios.history.get.length).toBe(1);
        mockAxios.restore();
    });
    it('displays the correct card title', () => {
        const wrapper = shallowMount(latestProductReport);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Latest Product Report');
    });

});

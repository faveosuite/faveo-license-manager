import axios from 'axios';
import { shallowMount, mount } from '@vue/test-utils';
import LatestInstallations from "../../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";

jest.mock('axios');

describe('LatestInstallations', () => {

    it('should fetch data and update the data property', async () => {
        const mockData = [
            { installation_id: 1, product_id: 123, installation_ip: '127.0.0.1', installation_date: '2023-06-18', installation_domain: 'example.com' },
        ];

        axios.get.mockResolvedValue({ data: { data: { afl_latest_installation: mockData } } });

        const wrapper = shallowMount(LatestInstallations);
        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(mockData);
        expect(wrapper.vm.loading).toBe(false);
        expect(axios.get).toHaveBeenCalledWith('/api/admin/dashboarddropdown');
    });

    it('renders without errors', () => {
        const wrapper = shallowMount(LatestInstallations);
        expect(wrapper.exists()).toBe(true);
    });
    it('renders the correct card title', () => {
        const wrapper = mount(LatestInstallations);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Mock translated value for latest_installations');
    });


});



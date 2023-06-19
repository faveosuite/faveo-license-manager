import axios from 'axios';
import { shallowMount } from '@vue/test-utils';
import LatestInstallations from "../../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";

jest.mock('axios');

describe('LatestInstallations', () => {

    it('should set columns and headings correctly', () => {
        const wrapper = shallowMount(LatestInstallations);
        const expectedColumns = ['installation_id', 'product_id', 'license_code', 'installation_date', 'installation_ip', 'installation_domain'];
        const expectedHeadings = {
            installation_id: 'Id',
            product_id: 'Product Id',
            installation_date: 'Date',
            installation_ip: 'IP',
            installation_domain: 'Domain',
        };

        expect(wrapper.vm.columns).toEqual(expectedColumns);
        expect(wrapper.vm.options.headings).toEqual(expectedHeadings);
    });

    it('should fetch data and update the data property', async () => {
        const mockData = [
            { installation_id: 1, product_id: 123, installation_ip: '127.0.0.1', installation_date: '2023-06-18', installation_domain: 'example.com' },
            // Add more sample data if needed
        ];

        axios.get.mockResolvedValue({ data: { data: { afl_latest_installation: mockData } } });

        const wrapper = shallowMount(LatestInstallations);
        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(mockData);
        expect(wrapper.vm.loading).toBe(false);
        expect(axios.get).toHaveBeenCalledWith('/api/admin/dashboarddropdown');
    });
});



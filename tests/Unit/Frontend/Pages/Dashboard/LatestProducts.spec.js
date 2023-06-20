import {mount, shallowMount} from '@vue/test-utils';
import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";
import axios from "axios";
describe('LatestVersion', () => {
    it('renders without errors', () => {
        const wrapper = shallowMount(LatestProducts);
        expect(wrapper.exists()).toBe(true);
    });
    // it('should fetch data and update the data property', async () => {
    //     const mockData = [
    //         { installation_id: 1, product_id: 123, installation_ip: '127.0.0.1', installation_date: '2023-06-18', installation_domain: 'example.com' },
    //         // Add more sample data if needed
    //     ];
    //
    //     axios.get.mockResolvedValue({ data: { data: { afl_latest_installation: mockData } } });
    //
    //     const wrapper = shallowMount(LatestProducts);
    //     await wrapper.vm.getData();
    //
    //     expect(wrapper.vm.data).toEqual(mockData);
    //     expect(wrapper.vm.loading).toBe(false);
    //     expect(axios.get).toHaveBeenCalledWith('/api/admin/dashboarddropdown');
    // });

    it('renders without errors', () => {
        const wrapper = shallowMount(LatestProducts);
        expect(wrapper.exists()).toBe(true);
    });
    it('renders the correct card title', () => {
        const wrapper = mount(LatestProducts);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Latest Products');
    });
    it('renders the correct card title', () => {
        const wrapper = mount(LatestProducts);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Latest Products');
    });
});

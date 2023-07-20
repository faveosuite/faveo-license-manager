import { mount, shallowMount } from '@vue/test-utils';
import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";

describe('LatestProducts', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(LatestProducts);
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly', async () => {
        const mock = new MockAdapter(axios);

        const responseData = {
            data: {
                latest_products: [
                    {
                        version_id: 1,
                        version_date: '2023-06-01',
                        version_expire_date: '2023-06-15',
                        version_number: '1.0',
                    },
                ],
            },
        };
        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);
        const wrapper = mount(LatestProducts);
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.data).toEqual(responseData.data.latest_products);
        expect(wrapper.vm.loading).toBe(false);
    });


});

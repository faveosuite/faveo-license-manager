import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import { shallowMount } from '@vue/test-utils';
import LatestProducts from "../../../../resources/js/Pages/Dashboard/LatestProducts.vue";

describe('LatestProducts', () => {
    let mock;
    let wrapper;

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(LatestProducts);
    });

    afterEach(() => {
        mock.reset();
    });

    it('should fetch and display data', async () => {
        const responseData = {
            data: {
                latest_products: [
                    { product_id: '123', product_title: 'Product 1', product_description: 'Description 1' },
                    { product_id: '456', product_title: 'Product 2', product_description: 'Description 2' },
                ],
            },
        };

        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(responseData.data.latest_products);
        expect(wrapper.find('.product_id').text()).toBe('123');
        expect(wrapper.find('.product_title').text()).toBe('Product 1');
        expect(wrapper.find('.product_description').text()).toBe('Description 1');
    });

    it('should handle API error', async () => {
        mock.onGet('/api/admin/dashboarddropdown').reply(500);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual([]);
    });
});

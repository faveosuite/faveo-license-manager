
import {mount, shallowMount} from '@vue/test-utils';
import ProductsIndex from "../../../../../resources/js/Pages/Product/ProductsIndex.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
describe('ProductsIndex', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(ProductsIndex);
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly',async() =>{
        const mock = new MockAdapter(axios);
        const responseData = {
            data: {
                product_date: "2022-09-21",
                product_id: 133,
                product_sku: "jferkjhkje",
                product_status: 1,
            },
        };
        mock.onGet('/api/admin/viewproducts').reply(200, responseData);
        const wrapper = mount(ProductsIndex);

        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);

    })
});

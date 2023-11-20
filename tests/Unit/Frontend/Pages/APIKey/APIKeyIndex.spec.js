
import {mount, shallowMount} from '@vue/test-utils';
import APIKeyIndex from "../../../../../resources/js/Pages/APIKey/APIKeyIndex.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
describe('ProductsIndex', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(APIKeyIndex);
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly',async() =>{
        const mock = new MockAdapter(axios);
        const responseData = {
            data: {
                api_key_licenses_add: 1,
                api_key_licenses_edit: 1,
                api_key_products_add: 1,
                api_key_products_edit: 1,
                api_key_search: 1,
                api_key_secret: "5hDuaXuTh9gTLfPL",
            },
        };
        mock.onGet('/api/admin/viewApiKeys').reply(200, responseData);
        const wrapper = mount(APIKeyIndex);

        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);

    })
});

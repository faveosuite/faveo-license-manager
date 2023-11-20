
import {mount, shallowMount} from '@vue/test-utils';
import LicensesIndex from "../../../../../resources/js/Pages/License/LicensesIndex.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
describe('LicensesIndex', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(LicensesIndex);
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly',async() =>{
        const mock = new MockAdapter(axios);
        const responseData = {
            data: {

                latest_license: "2022-02-17",
                license_code: "5hDuaXuTh9gTLfPL",
                license_status: 1,
                product_title: "Helpdesk Enterprise"
            },
        };
        mock.onGet('/api/admin/viewLicenses').reply(200, responseData);
        const wrapper = mount(LicensesIndex);

        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);

    })
});


import {mount, shallowMount} from '@vue/test-utils';
import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
describe('ClientsIndex', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(ClientsIndex);
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly',async() =>{
        const mock = new MockAdapter(axios);
        const responseData = {
            data: {
                client_active_date: "2022-06-21",
                client_cancel_date: "0000-00-00",
                client_email: "anil@gmail.com",
                client_id: 3,
                client_status: 1,
                full_name: "Sowmya Gyui",
            },
        };
        mock.onGet('/api/admin/viewClients').reply(200, responseData);
        const wrapper = mount(ClientsIndex);

        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);

    })
});

import { mount } from '@vue/test-utils';
import { createStore } from 'vuex';
import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';

describe('ClientsIndex', () => {
    // Create a mock Vuex store
    const store = createStore({
        getters: {
            getUserData: () => ({ client_id: 12}),
        },
    });
    const emitter = {
        on: jest.fn(),
    };

    it('renders component correctly', async () => {
        const wrapper = mount(ClientsIndex, {
            global: {
                plugins: [store],
                mocks: {
                    emitter,
                },
            },
        });
        await wrapper.vm.$nextTick();

        expect(wrapper.exists()).toBe(true);
    });

    it('renders without errors', () => {
        const wrapper = mount(ClientsIndex, {
            global: {
                plugins: [store],
                mocks: {
                    emitter,
                },
            },
        });
        expect(wrapper.exists()).toBe(true);
    });

    it('fetches data from the API correctly', async () => {
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
        const wrapper = mount(ClientsIndex, {
            global: {
                plugins: [store],
                mocks: {
                    emitter,
                },
            },
        });

        await wrapper.vm.$nextTick();
        expect(wrapper.exists()).toBe(true);

    });

});

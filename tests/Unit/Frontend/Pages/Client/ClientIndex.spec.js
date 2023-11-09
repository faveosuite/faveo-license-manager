import { mount } from '@vue/test-utils';
import ClientsIndex from "../../../../../resources/js/Pages/Client/ClientsIndex.vue";
import axios from 'axios';

jest.mock('axios', () => ({
    get: jest.fn(() => Promise.resolve({ data: { data: [] } })),
}));

describe('ClientsIndex', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(ClientsIndex, {
            data() {
                return {
                    loading: false,
                    emitter: {
                        on: jest.fn(),
                    },
                };
            },
        });
    });

    it('should have a valid initial state', () => {
        expect(wrapper.vm.columns).toEqual(['full_name', 'client_email', 'client_active_date', 'client_status', 'actions']);
    });

    it('should fetch data when created', () => {
        expect(axios.get).toHaveBeenCalledWith('/api/admin/viewClients');
    });

    it('should update data when the "refreshData" event is emitted', () => {
        wrapper.vm.$emit('refreshData');
        expect(axios.get).toHaveBeenCalledTimes(3);
    });

    it('should display loading when data is being fetched', async () => {
        expect(wrapper.find('.custom-loader').exists()).toBeFalsy();
        await wrapper.setData({ loading: true });
        expect(wrapper.find('.custom-loader').exists()).toBeTruthy();
    });

    it('should render the client table when data is available', async () => {
        const testData = [
            {
                full_name: 'John Doe',
                client_email: 'johndoe@example.com',
                client_active_date: '2023-11-07',
                client_status: true,
            },
        ];
        await wrapper.setData({ data: testData });
        expect(wrapper.find('.v-client-table').exists()).toBeTruthy();
    });
});

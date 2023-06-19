import ExpiringVersion from "../../../../../resources/js/Pages/Dashboard/ExpiringVersion.vue";
import {mount, shallowMount} from '@vue/test-utils';
import axios from "axios";
import MockAdapter from "axios-mock-adapter";

describe('ExpiringVersion', () => {


    it('renders without errors', () => {
        const wrapper = shallowMount(ExpiringVersion);
        expect(wrapper.exists()).toBe(true);
    });
    it('renders the correct card title', () => {
        const wrapper = mount(ExpiringVersion);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Expiring Version');
    });
    it('renders the correct card title', () => {
        const wrapper = mount(ExpiringVersion);

        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Expiring Version');
    });
    it('fetches data from the API correctly', async () => {
        // Create a new instance of the Axios mock adapter
        const mock = new MockAdapter(axios);

        // Mock the API response
        const responseData = {
            data: {
                expired_versions: [
                    {
                        version_id: 1,
                        version_date: '2023-06-01',
                        version_expire_date: '2023-06-15',
                        version_number: '1.0',
                    },
                    // Add more sample data if needed
                ],
            },
        };
        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        // Mount the component
        const wrapper = mount(ExpiringVersion);

        // Wait for the API request to complete
        await wrapper.vm.$nextTick();

        // Check if the data is fetched and assigned correctly
        expect(wrapper.vm.data).toEqual(responseData.data.expired_versions);

        // Optionally, you can also check if the loading state is updated
        expect(wrapper.vm.loading).toBe(false);
    });


});

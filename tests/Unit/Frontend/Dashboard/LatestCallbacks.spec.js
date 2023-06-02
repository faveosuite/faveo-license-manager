import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import LatestCallbacks from "../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";

describe('LatestCallbacks', () => {
    let mock;

    beforeEach(() => {
        // Create a new instance of the MockAdapter
        mock = new MockAdapter(axios);
    });

    afterEach(() => {
        // Restore mock adapter on each test
        mock.restore();
    });

    it('fetches and renders the latest callbacks', async () => {
        // Mock the response data
        const responseData = {
            data: {
                afu_latest_callbacks: [
                    { version: '1.0', type: 'callback', ip: '127.0.0.1', date: '2023-06-01', status: 'success' },
                    { version: '1.1', type: 'callback', ip: '192.168.0.1', date: '2023-06-02', status: 'failed' }
                ]
            }
        };

        // Set up the mock response for the GET request
        mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);

        // Mount the component
        const wrapper = shallowMount(LatestCallbacks);

        // Wait for the axios request to complete
        await axios.get('/api/admin/dashboarddropdown');

        // Verify that the data is rendered correctly
        expect(wrapper.vm.data).toEqual(responseData.data.afu_latest_callbacks);
        expect(wrapper.findAll('tr')).toHaveLength(3); // Including the table header row

        // Verify that the table columns are rendered correctly
        const tableHeaders = wrapper.findAll('th').wrappers.map((th) => th.text());
        expect(tableHeaders).toEqual(['Version', 'Type', 'IP', 'Date', 'Status']);

        // Verify that the table rows are rendered correctly
        const tableRows = wrapper.findAll('tbody tr').wrappers;
        expect(tableRows).toHaveLength(2);
        expect(tableRows[0].text()).toContain('1.0');
        expect(tableRows[1].text()).toContain('1.1');
    });
});

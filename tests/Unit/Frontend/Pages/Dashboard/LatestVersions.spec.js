import {mount, shallowMount} from '@vue/test-utils';
import latestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
import LatestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";

describe('LatestVersion', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(latestVersions);
        expect(wrapper.exists()).toBe(true);
    });
    it('displays the correct card title', () => {
        const wrapper = shallowMount(latestVersions);
        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Mock translated value for latest_version');
    });
    it('fetches data from the API correctly', async () => {
        const mock = new MockAdapter(axios);

        const responseData = {
            data: {
                latest_versions: [
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
        const wrapper = mount(LatestVersions);
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.data).toEqual(responseData.data.latest_versions);
        expect(wrapper.vm.loading).toBe(false);
    });


});

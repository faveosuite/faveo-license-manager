// import { mount } from "@vue/test-utils";
// import globalMixins from "../../../../../resources/js/globalMixins";
// import { createStore } from "vuex";
// import axios from "axios";
// import MockAdapter from "axios-mock-adapter";
// import LatestCallbacks from "../../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";
// const store = createStore({});
//
// let wrapper;
// let mockAxios = new MockAdapter(axios);
// const fakeRequestData = {
//     'success':true,
//     'data':{}
// }
// describe("LatestCallbacks", () => {
//     const updateWrapper = () => {
//         wrapper = mount(LatestCallbacks, {
//             global: {
//                 plugins: [store],
//                 mixins: [globalMixins],
//                 stubs: ["data-table", "data-table-stub"],
//             },
//         });
//     };
//
//     beforeEach(() => {
//         updateWrapper();
//         mockAxios.reset();
//     });
//
//     afterEach(() => {
//         mockAxios.restore();
//     });
//
//     it("makes an API call when 'getData' method  called", async() => {
//         updateWrapper();
//
//         stubRequest();
//         await wrapper.vm.getData()
//         setTimeout(() => {
//             expect(wrapper.vm.loading).toBe(false);
//             expect(wrapper.vm.data).toEqual('fakeRequestData');
//             expect(mockAxios.history.get[0].url).toBe("/api/admin/dashboarddropdown");
//             done()
//         }, 10)
//     });
//
//
//     it("makes `loading` as false when api returns error", async () => {
//         updateWrapper();
//
//         stubRequest(400);
//
//         await wrapper.vm.getData();
//         setTimeout(() => {
//             expect(wrapper.vm.loading).toEqual(false)
//             expect(wrapper.vm.data).toEqual('');
//             expect(mockAxios.history.get[0].url).toBe("/api/admin/dashboarddropdown");
//         }, 1);
//     });
//     function stubRequest(status = 200,url = '/api/admin/dashboarddropdown'){
//
//         mockAxios.onGet(url).reply(status,fakeRequestData)
//
//     }
// })
//
import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import latestCallbacks from "../../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";

jest.mock('axios');

describe('LatestCallbacks', () => {
    it('displays fetched data correctly', async () => {
        const mockedData = [
            {
                callback_id: 1,
                license_code: 'ABC123',
                callback_ip: '192.168.0.1',
                callback_date_time: '2023-06-19 10:00:00',
                callback_domain: 'example.com',
            },
            // Add more mocked data as needed
        ];

        axios.get.mockResolvedValue({ data: { data: { afu_latest_callbacks: mockedData } } });

        const wrapper = shallowMount(latestCallbacks);
        await wrapper.vm.$nextTick();

        const tableData = wrapper.vm.data;
        expect(tableData).toEqual(mockedData);
    });
    it('displays loading state during data retrieval', async () => {
        const wrapper = shallowMount(latestCallbacks);

        // Simulate data retrieval in the "beforeMount" hook
        expect(wrapper.vm.loading).toBe(true);

        // Simulate data retrieval completion
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.loading).toBe(false);
    });

});

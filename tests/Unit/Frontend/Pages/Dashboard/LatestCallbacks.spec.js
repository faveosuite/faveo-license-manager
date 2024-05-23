import LatestCallbacks from "../../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('LatestCallbacks', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestCallbacks,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "callback_domain" : '122.33.44.55',
                        "callback_date_time" : '23-1-300',
                        "callback_ip" : '23.11.22.33',
                        "callback_status" : "Inactive"
                    }
                ]
            },

        })
    }

    beforeEach(() => {

        updateWrapper();
    })

    const data = [
        {
            "callback_domain" : '122.33.44.55',
            "callback_date_time" : '23-1-300',
            "callback_ip" : '23.11.22.33',
            "callback_status" : "Inactive"
        }
    ]

    it('renders without errors', () => {

        expect(wrapper.exists()).toBe(true);
    });

    it('client-table should exist when page created', async() => {

        await expect(wrapper.find('v-client-table-stub').exists()).toBe(true)
    })

    it("properly load props in data in template option of datatable", () => {

        expect(wrapper.vm.data).toEqual(data);
    })

    it("return columns in template option of datatable", () => {

        expect(wrapper.vm.options.templates.callback_domain('test', {'callback_domain': '23-1-300'})).toEqual("23-1-300")

        expect(wrapper.vm.options.templates.callback_date_time('test', {'callback_date_time': '15-16-1000'})).toEqual("15-16-1000")

        expect(wrapper.vm.options.templates.callback_ip('test', {'callback_ip': '15161000'})).toEqual("15161000")
    })

});




// import { shallowMount } from '@vue/test-utils';
// import axios from 'axios';
// import latestCallbacks from "../../../../../resources/js/Pages/Dashboard/LatestCallbacks.vue";
//
// jest.mock('axios');
//
// describe('LatestCallbacks', () => {
//
//     it('displays fetched data correctly', async () => {
//         const mockedData = [
//             {
//                 callback_id: 1,
//                 license_code: 'ABC123',
//                 callback_ip: '192.168.0.1',
//                 callback_date_time: '2023-06-19 10:00:00',
//                 callback_domain: 'example.com',
//             },
//         ];
//
//         axios.get.mockResolvedValue({ data: { data: { afu_latest_callbacks: mockedData } } });
//
//         const wrapper = shallowMount(latestCallbacks);
//         await wrapper.vm.$nextTick();
//
//         const tableData = wrapper.vm.data;
//         expect(tableData).toEqual(mockedData);
//     });
//     it('displays loading state during data retrieval', async () => {
//         const wrapper = shallowMount(latestCallbacks);
//
//         // Simulate data retrieval in the "beforeMount" hook
//         expect(wrapper.vm.loading).toBe(true);
//
//         // Simulate data retrieval completion
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.loading).toBe(false);
//     });
//
// });

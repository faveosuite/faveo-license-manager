import LatestProductReport from "../../../../../resources/js/Pages/Dashboard/LatestProductReport.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('LatestProductReport', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestProductReport,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "report_text" : 'default text',
                        "report_date_time" : '23-1-300',
                        "license_code" : '23.11.22.33',
                        "report_status" : "Inactive"
                    }
                ],

                generalSetting : {
                    time_format : {js_format:81},
                    timezone : {name : 'Asia/Kolkata'},
                    date_format : {js_format : 8765}
                }
            },

        })
    }

    beforeEach(() => {

        updateWrapper();
    })

    const data = [
        {
            "report_text" : 'default text',
            "report_date_time" : '23-1-300',
            "license_code" : '23.11.22.33',
            "report_status" : "Inactive"
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

        expect(wrapper.vm.options.templates.license_code('test', {'license_code': '123456789098'})).toEqual("1234-5678-9098")

        // expect(wrapper.vm.options.templates.report_date_time('test', {'report_date_time': '15-6-2000'})).toEqual("15-6-2000")

    })

});



// import { shallowMount } from '@vue/test-utils';
// import latestProductReport from "../../../../../resources/js/Pages/Dashboard/LatestProductReport.vue";
// import MockAdapter from "axios-mock-adapter";
// import axios from "axios";
// import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";
//
// let wrapper;
// describe('LatestProductReport', () => {
//     it('renders without errors', () => {
//         const wrapper = shallowMount(latestProductReport);
//         expect(wrapper.exists()).toBe(true);
//     });
//     it('fetches data from API', async () => {
//         const mockAxios = new MockAdapter(axios);
//
//         const responseData = {
//             data: {
//                 latest_product_reports: [
//                     { report_id: 1, report_date_time: '2023-06-18', status: 'Pending' },
//                     { report_id: 2, report_date_time: '2023-06-19', status: 'Completed' },
//                 ],
//             },
//         };
//         mockAxios.onGet('/api/admin/dashboarddropdown').reply(200, responseData);
//         const wrapper = shallowMount(latestProductReport);
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.data).toEqual(responseData.data.latest_product_reports);
//         expect(mockAxios.history.get[0].url).toBe('/api/admin/dashboarddropdown');
//         expect(mockAxios.history.get.length).toBe(1);
//         mockAxios.restore();
//     });
//
// });

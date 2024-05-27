import LatestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";
import {mount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('LatestVersions', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestVersions,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "version_number" : '122.33.44.55',
                        "version_date" : '23-1-300',
                        "version_expire_date" : '23.11.22.33',
                        "version_status" : "Inactive"
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
            "version_number" : '122.33.44.55',
            "version_date" : '23-1-300',
            "version_expire_date" : '23.11.22.33',
            "version_status" : "Inactive"
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

        expect(wrapper.vm.options.templates.version_number('test', {'version_number': '238765300'})).toEqual("238765300")

        // expect(wrapper.vm.options.templates.version_date('test', {'version_date': '15-16-1000'})).toEqual("15-16-1000")

        // expect(wrapper.vm.options.templates.version_expire_date('test', {'version_expire_date': '15161000'})).toEqual("15161000")
    })

});







// import {mount, shallowMount} from '@vue/test-utils';
// import latestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";
// import MockAdapter from "axios-mock-adapter";
// import axios from "axios";
// import LatestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";
//
// describe('LatestVersion', () => {
//
//     it('renders without errors', () => {
//         const wrapper = shallowMount(latestVersions);
//         expect(wrapper.exists()).toBe(true);
//     });
//
//     it('fetches data from the API correctly', async () => {
//         const mock = new MockAdapter(axios);
//
//         const responseData = {
//             data: {
//                 latest_versions: [
//                     {
//                         version_id: 1,
//                         version_date: '2023-06-01',
//                         version_expire_date: '2023-06-15',
//                         version_number: '1.0',
//                     },
//                 ],
//             },
//         };
//         mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);
//         const wrapper = mount(LatestVersions);
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.data).toEqual(responseData.data.latest_versions);
//         expect(wrapper.vm.loading).toBe(false);
//     });
//
//
// });

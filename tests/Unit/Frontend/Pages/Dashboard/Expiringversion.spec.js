
import ExpiringVersion from "../../../../../resources/js/Pages/Dashboard/ExpiringVersion.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('ExpiringVersion', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(ExpiringVersion,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "version_number" : '122',
                        "version_date" : '23-1-300',
                        "version_expire_date" : '23-11-2222',
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
            "version_number" : '122',
            "version_date" : '23-1-300',
            "version_expire_date" : '23-11-2222',
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

        expect(wrapper.vm.options.templates.version_number('test', {'version_number': '15161000'})).toEqual("15161000")
    })

});

// it('fetches data from the API correctly', async () => {
//     const mock = new MockAdapter(axios);
//
//     const responseData = {
//         data: {
//             expired_versions: [
//                 {
//                     version_id: 1,
//                     version_date: '2023-06-01',
//                     version_expire_date: '2023-06-15',
//                     version_number: '1.0',
//                 },
//             ],
//         },
//     };
//     mock.onGet('/api/admin/dashboarddropdown').reply(200, responseData);
//     const wrapper = mount(ExpiringVersion);
//     await wrapper.vm.$nextTick();
//     expect(wrapper.vm.data).toEqual(responseData.data.expired_versions);
//     expect(wrapper.vm.loading).toBe(false);
// });

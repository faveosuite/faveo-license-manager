import LatestInstallations from "../../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";
import {mount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('LatestInstallations', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestInstallations,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "license_code" : '122.33.44.55',
                        "installation_ip" : '23-1-300',
                        "installation_date" : '23.11.22.33',
                        "installation_domain" : "Inactive"
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
            "license_code" : '122.33.44.55',
            "installation_ip" : '23-1-300',
            "installation_date" : '23.11.22.33',
            "installation_domain" : "Inactive"
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

        expect(wrapper.vm.options.templates.license_code('test', {'license_code': '238765300'})).toEqual("2387-6530-0")

        expect(wrapper.vm.options.templates.installation_ip('test', {'installation_ip': '15-16-1000'})).toEqual("15-16-1000")

        expect(wrapper.vm.options.templates.installation_date('test', {'installation_date': '15161000'})).toEqual("15161000")
    })

});






// import axios from 'axios';
// import { shallowMount, mount } from '@vue/test-utils';
// import LatestInstallations from "../../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";
//
// jest.mock('axios');
//
// describe('LatestInstallations', () => {
//
//     it('should fetch data and update the data property', async () => {
//         const mockData = [
//             { installation_id: 1, product_id: 123, installation_ip: '127.0.0.1', installation_date: '2023-06-18', installation_domain: 'example.com' },
//         ];
//
//         axios.get.mockResolvedValue({ data: { data: { afl_latest_installation: mockData } } });
//
//         const wrapper = shallowMount(LatestInstallations);
//         await wrapper.vm.getData();
//
//         expect(wrapper.vm.data).toEqual(mockData);
//         expect(wrapper.vm.loading).toBe(false);
//         expect(axios.get).toHaveBeenCalledWith('/api/admin/dashboarddropdown');
//     });
//
//     it('renders without errors', () => {
//         const wrapper = shallowMount(LatestInstallations);
//         expect(wrapper.exists()).toBe(true);
//     });
//
// });
//
//

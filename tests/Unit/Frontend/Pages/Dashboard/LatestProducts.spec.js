import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

describe('LatestProducts', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestProducts,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "product_title" : 'default text',
                        "product_sku" : '23-1-300',
                        "product_date" : '23.11.22.33',
                        "product_status" : "Inactive"
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
            "product_title" : 'default text',
            "product_sku" : '23-1-300',
            "product_date" : '23.11.22.33',
            "product_status" : "Inactive"
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

        expect(wrapper.vm.options.templates.product_title('test', {'product_title': 'default text'})).toEqual("default text")
    })

});






// import { mount, shallowMount } from '@vue/test-utils';
// import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";
// import axios from "axios";
// import MockAdapter from "axios-mock-adapter";
//
// describe('LatestProducts', () => {
//
//     it('renders without errors', () => {
//         const wrapper = shallowMount(LatestProducts);
//         expect(wrapper.exists()).toBe(true);
//     });
//
//     it('fetches data from the API correctly', async () => {
//         const mock = new MockAdapter(axios);
//
//         const responseData = {
//             data: {
//                 latest_products: [
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
//         const wrapper = mount(LatestProducts);
//         await wrapper.vm.$nextTick();
//         expect(wrapper.vm.data).toEqual(responseData.data.latest_products);
//         expect(wrapper.vm.loading).toBe(false);
//     });
//
//
// });

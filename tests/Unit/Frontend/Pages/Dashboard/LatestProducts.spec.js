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
                ],

                generalSetting : {
                    time_format : {js_format: "HH:mm"},
                    timezone : {name : 'Asia/Kolkata'},
                    date_format : {js_format : "DD-MM-YYYY"}
                }
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

});

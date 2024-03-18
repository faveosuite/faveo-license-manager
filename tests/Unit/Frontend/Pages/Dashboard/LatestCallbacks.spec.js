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

        expect(wrapper.vm.options.templates.callback_date_time('test', {'callback_date_time': '2020-10-22'})).toEqual("22-10-2020")

        expect(wrapper.vm.options.templates.callback_ip('test', {'callback_ip': '15161000'})).toEqual("15161000")
    })

});

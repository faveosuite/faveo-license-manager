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

        expect(wrapper.vm.options.templates.report_date_time('test', {'report_date_time': '2000-6-5'})).toEqual("05-06-2000")

    })

});

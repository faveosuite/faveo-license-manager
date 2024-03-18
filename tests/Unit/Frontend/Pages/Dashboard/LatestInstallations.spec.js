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

        expect(wrapper.vm.options.templates.installation_ip('test', {'installation_ip': '15-16-1000 00:00'})).toEqual("15-16-1000 00:00")

        expect(wrapper.vm.options.templates.installation_date('test', {'installation_date': '2020-10-22'})).toEqual("22-10-2020")
    })

});

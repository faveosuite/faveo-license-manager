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

        expect(wrapper.vm.options.templates.version_date('test', {'version_date': '2000-2-3'})).toEqual("03-02-2000")

        expect(wrapper.vm.options.templates.version_expire_date('test', {'version_expire_date': '2020-2-10'})).toEqual("10-02-2020")
    })

});

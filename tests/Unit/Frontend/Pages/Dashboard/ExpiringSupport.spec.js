
import ExpiringSupport from "../../../../../resources/js/Pages/Dashboard/ExpiringSupport.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";

jest.mock('../../../../../resources/js/helpers/extraLogics', ()=>({
    formatDateTime: jest.fn((value)=>{ return value }),
    lang: jest.fn()
}));
describe('ExpiringSupport', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(ExpiringSupport,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "license_code" : 'OASEDIRURYBD',
                        "product" : 'Test Product',
                        "license_date" : '23-11-2222',
                        "license_update_date": '23-11-2222',
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
            "license_code" : 'OASEDIRURYBD',
            "product" : 'Test Product',
            "license_date" : '23-11-2222',
            "license_update_date": '23-11-2222',
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

        expect(wrapper.vm.options.templates.license_date('test', {'license_date': '2020-10-22'})).toEqual("2020-10-22")

        expect(wrapper.vm.options.templates.license_support_date('test', {'license_support_date': '2020-10-22'})).toEqual("2020-10-22")

    })

});


import LatestClients from "../../../../../resources/js/Pages/Dashboard/LatestClients.vue";
import {mount, shallowMount} from '@vue/test-utils';
import store from "../../../../../resources/js/store";
import { formatDateTime, lang } from '../../../../../resources/js/helpers/extraLogics'

jest.mock('../../../../../resources/js/helpers/extraLogics', ()=>({
    formatDateTime: jest.fn(value => value),
    lang: jest.fn()
}));

describe('LatestClients', () => {

    let wrapper;

    const updateWrapper = () =>{

        wrapper = mount(LatestClients,{

            global : {
                stubs: ['v-client-table'],
                plugins : [store],

            },

            props : {

                data: [
                    {
                        "full_name" : 'Test Name',
                        "client_email" : 'test@testing.com',
                        "client_active_date" : '23-11-2222',
                        "license_count": 3,
                        "client_status" : "Inactive"
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
            "full_name" : 'Test Name',
            "client_email" : 'test@testing.com',
            "client_active_date" : '23-11-2222',
            "license_count": 3,
            "client_status" : "Inactive"
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

        expect(wrapper.vm.options.templates.client_active_date('test', {'client_active_date': '2020-10-22'})).toEqual("2020-10-22")
    })

});

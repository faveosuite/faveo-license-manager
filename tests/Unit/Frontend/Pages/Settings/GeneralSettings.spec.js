import { mount, shallowMount } from '@vue/test-utils'

import GeneralSettings from "../../../../../resources/js/Pages/Settings/GeneralSettings";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateGeneralSettings";

import {createStore} from "vuex";

import MockAdapter from 'axios-mock-adapter';

import axios from 'axios';

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

jest.mock('cropperjs/dist/cropper.css', () => {
    return {};
});

const store = createStore({

    getters() {

        return {

            getApiKey: () => { return '' },

            getUserToken: () => { return '' }
        }
    },

    actions: {

        fetchSettings: jest.fn()
    }
})

describe('GeneralSettings', () => {

    let wrapper;

    let mockAxios = new MockAdapter(axios);


    const updateWrapper = () => {

        wrapper = mount(GeneralSettings, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','dynamic-select']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
        mockAxios = new MockAdapter(axios);

    });

    it('submits form data successfully', async () => {

        await wrapper.vm.onSubmit();

        mockAxios.onPost('/api/admin/common-setting').reply(200, { data: 'success' });

        await wrapper.setData({
            google_site_key: 'asdfghjkl',
            google_secret_key: 'dfghjkl',
            agora_invoicing_url: 'qwertyuioiuytrewerthj',
            date_format: { id: 'Enabled', js_format: 1 },
            time_format: { id: '10', js_format: 10 },
            timezone: { id: '20', js_format: 20 },
        });

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error', async () => {
        mockAxios.onPost('/api/admin/common-setting').reply(500, { error: 'Internal Server Error' });

        await wrapper.setData({
            google_site_key: 'asdfghjkl',
            google_secret_key: 'dfghjkl',
            agora_invoicing_url: 'qwertyuioiuytrewerthj',
            date_format: { id: 'Enabled', js_format: 1 },
            time_format: { id: '10', js_format: 10 },
            timezone: { id: '20', js_format: 20 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });
})

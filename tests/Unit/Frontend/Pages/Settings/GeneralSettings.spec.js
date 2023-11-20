import { mount, shallowMount } from '@vue/test-utils'

import GeneralSettings from "../../../../../resources/js/Pages/Settings/GeneralSettings";

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/validateGeneralSettings";

import {createStore} from "vuex";

import MockAdapter from 'axios-mock-adapter';

import axios from 'axios';

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

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

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange({id:1,name:'test'},'SMART_REPORTS');

        expect(wrapper.vm.smartReportsType).toEqual({id:1,name:'test'});
    });

    it('isValid - should return false ', done => {

        validation.validateGeneralSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateGeneralSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('isValid should return true', () => {
        wrapper.vm.validateGeneralSettings = jest.fn(() => ({ errors: [], isValid: true }));
        expect(wrapper.vm.isValid()).toBe(true);
    });

    it('submits form data successfully', async () => {
        mockAxios.onPost('/api/admin/generalsettings/new').reply(200, { data: 'success' });

        await wrapper.setData({
            smartReportsType: { name: 'Enabled', value: 1 },
            smartTablesType: { name: 'Enabled', value: 1 },
            selectedTimezone: { name: 'UTC', value: 'UTC' },
            archiveOlderRecordsType: { name: 'Enabled', value: 1 },
            recordsPerPageType: { name: '10', value: 10 },
            recordIndexPageType: { name: '20', value: 20 },
            searchLimitType: { name: '30', value: 30 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });

    it('handles submission error', async () => {
        mockAxios.onPost('/api/admin/generalsettings/new').reply(500, { error: 'Internal Server Error' });

        await wrapper.setData({
            smartReportsType: { name: 'Enabled', value: 1 },
            smartTablesType: { name: 'Enabled', value: 1 },
            selectedTimezone: { name: 'UTC', value: 'UTC' },
            archiveOlderRecordsType: { name: 'Enabled', value: 1 },
            recordsPerPageType: { name: '10', value: 10 },
            recordIndexPageType: { name: '20', value: 20 },
            searchLimitType: { name: '30', value: 30 },
        });

        await wrapper.vm.onSubmit();

        expect(wrapper.vm.loading).toBe(false);
    });
})

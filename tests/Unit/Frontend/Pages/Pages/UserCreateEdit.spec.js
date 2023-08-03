import { mount, shalowMount } from '@vue/test-utils'

import UserCreateEdit from "../../../../../resources/js/Pages/Users/UserCreateEdit.vue";

import globalMixins from "../../../../../resources/js/globalMixins";

import {createStore, creatStore} from "vuex";
import * as validation from "../../../../../resources/js/helpers/validator/clientValidation";

import {validateUserSettings} from "../../../../../resources/js/helpers/validator/userSettings";

jest.mock('../../../../../resources/js/helpers/responseHandler');

jest.mock('../../../../../resources/js/helpers/extraLogics');

const store = createStore({

    getters() {

        return {

            getApiKey: () => { return '' },

            getUserToken: () => { return '' }
        }
    },
})

describe('UserCreateEdit',() =>{
    let wrapper;

    const updateWrapper = () => {
        wrapper = mount(UserCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['loader', 'custom-loader', 'alert', 'router-link', 'text-field', 'radio-button']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
    });

    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('name','user_fname');

        expect(wrapper.vm.user_fname).toEqual('name');
    });

    it('isValid - should return true ', done => {

        validation.validateUserSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });

    it('initializes with the correct default data', () => {
        expect(wrapper.vm.title).toBe('create_new_user');
        expect(wrapper.vm.iconClass).toBe('fas fa-save');
        expect(wrapper.vm.btnName).toBe('save');
        expect(wrapper.vm.hasDataPopulated).toBe(true);
        expect(wrapper.vm.loading).toBe(false);
        expect(wrapper.vm.radioOptions).toEqual([
            { name: 'active', value: 1 },
            { name: 'inactive', value: 0 },
        ]);
        expect(wrapper.vm.admin_fname).toBe('');
        expect(wrapper.vm.admin_lname).toBe('');
        expect(wrapper.vm.admin_status).toBe(1);
        expect(wrapper.vm.admin_email).toBe('');
        expect(wrapper.vm.apiEndpoint).toBe('/api/admin/addusers');
    });


})

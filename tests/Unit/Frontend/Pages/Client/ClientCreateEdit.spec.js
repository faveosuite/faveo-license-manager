import { mount, shallowMount } from '@vue/test-utils'

import ClientCreateEdit from '../../../../../resources/js/Pages/Client/ClientCreateEdit'

import globalMixins from "../../../../../resources/js/globalMixins";

import * as validation from "../../../../../resources/js/helpers/validator/clientValidation";

import {createStore} from "vuex";

import MockAdapter from 'axios-mock-adapter';
import {validateClientSettings} from "../../../../../resources/js/helpers/validator/clientValidation";
import axios from "axios";

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

describe('ClientCreateEdit', () => {

    let wrapper;
    let mockAxios = new MockAdapter(axios); // Use mockAxios instead of mock


    const updateWrapper = () => {

        wrapper = mount(ClientCreateEdit, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs : ['loader','custom-loader','alert','router-link','text-field','radio-button']
            }
        })
    }

    beforeEach(()=>{

        updateWrapper();
        mockAxios = new MockAdapter(axios);

    });

     it('renders the component', () => {
        expect(wrapper.exists()).toBe(true);
      });

      it('initializes with the correct data', () => {

        expect(wrapper.vm.title).toBe('create_new_contact');

        expect(wrapper.vm.iconClass).toBe('fas fa-save');

        expect(wrapper.vm.btnName).toBe('save');

        expect(wrapper.vm.hasDataPopulated).toBe(true);
      })
    it('`onChange` - method should update correct value to data',()=>{

        wrapper.vm.onChange('name','client_fname');

        expect(wrapper.vm.client_fname).toEqual('name');
    });

    it('`onChange` - method should update correct value for role', () => {

        wrapper.vm.onChange(0, 'client_role');
        expect(wrapper.vm.client_role).toBe(0);
    });

    it('`onChange` - method should set client_role to 1', () => {

        wrapper.vm.onChange(1, 'client_role');
        expect(wrapper.vm.client_role).toBe(1);
    });


    it('isValid - should return false ', done => {

        validation.validateClientSettings = () =>{return {errors : [], isValid : false}}

        expect(wrapper.vm.isValid()).toBe(false)

        done()
    });

    it('isValid - should return true ', done => {

        validation.validateClientSettings = () =>{return {errors : [], isValid : true}}

        expect(wrapper.vm.isValid()).toBe(true)

        done()
    });


    it('submits the form successfully', async () => {
        mockAxios.onPost('/api/admin/clients/add').reply(200, { data: {} });

        await wrapper.setData({
            client_fname: 'John',
            client_lname: 'Doe',
            client_email: 'john.doe@example.com',
            client_status: 1,
        });

        const onSubmitSpy = jest.spyOn(wrapper.vm, 'onSubmit');

        const saveButton = wrapper.find('.btn-primary');

        saveButton.trigger('click');

        expect(wrapper.vm.loading).toBe(true);
    });

})

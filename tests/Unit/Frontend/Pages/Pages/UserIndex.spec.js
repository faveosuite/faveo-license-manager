import { mount, shallowMount} from  "@vue/test-utils";
import UsersIndex from "../../../../../resources/js/Pages/Users/UsersIndex.vue";
import globalMixins from "../../../../../resources/js/globalMixins";
import {createStore} from "vuex";
import MockAdapter from "axios-mock-adapter";
import axios from "axios";
const store =createStore({});
let wrapper;
let mockAxios = new MockAdapter(axios);

const fakeRequestData = {
    'success':true,
    'data':{}
}

describe('UserIndex',()=> {
    const updateWrapper = () => {
        wrapper = mount(UsersIndex, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ['data-table','data-table-stub'],
            },
        });
    };

    beforeEach(() => {
        updateWrapper();
        mockAxios = new MockAdapter(axios);
    });

    afterEach(() => {
        mockAxios.restore();
    });

    it("makes an API call when 'getData' method called", async() => {
        updateWrapper();

        stubRequest();
        await wrapper.vm.getData();
        setTimeout(()=>{
            expect(wrapper.vm.loading).toBe(false);
            expect(wrapper.vm.data).toEqual(fakeRequestData);
            expect(mockAxios.history.get[0].url).toBe('/api/admin/users')
            done();
        },1);
    })

    it('makes `loading` as false when api returns error',async()=>{
        updateWrapper();

        stubRequest(400);

        await wrapper.vm.getData();
        expect(wrapper.vm.loading).toEqual(true)
        setTimeout(()=>{
            expect(wrapper.vm.loading).toEqual(false)
            expect(wrapper.vm.data).toEqual('');
            expect(mockAxios.history.get[0].url).toBe('/api/admin/users')
            done();
        },1);
    });

    function stubRequest(status = 200,url = '/api/admin/users'){

        mockAxios.onGet(url).reply(status,fakeRequestData)

    }
})

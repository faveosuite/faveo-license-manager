
import { mount, shallowMount} from "@vue/test-utils";
import ProductsIndex from "../../../../../resources/js/Pages/Product/ProductsIndex.vue";
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
describe('ProductsIndex',()=> {
    const updateWrapper = () => {
        wrapper = mount(ProductsIndex, {
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
            expect(mockAxios.history.get[0].url).toBe('/api/admin/viewproducts')
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
            expect(mockAxios.history.get[0].url).toBe('/api/admin/viewproducts')
            done();
        },1);
    });



    function stubRequest(status = 200,url = '/api/admin/reportSystem'){

        mockAxios.onGet(url).reply(status,fakeRequestData)

    }
})



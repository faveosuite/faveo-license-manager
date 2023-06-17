import { mount } from "@vue/test-utils";
import globalMixins from "../../../../../resources/js/globalMixins";
import { createStore } from "vuex";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
import latestInstallations from "../../../../../resources/js/Pages/Dashboard/LatestInstallations.vue";
const store = createStore({});

let wrapper;
let mockAxios = new MockAdapter(axios);
const fakeRequestData = {
    'success':true,
    'data':{}
}
describe("latestInstallations", () => {
    const updateWrapper = () => {
        wrapper = mount(latestInstallations, {
            global: {
                plugins: [store],
                mixins: [globalMixins],
                stubs: ["data-table", "data-table-stub"],
            },
        });
    };

    beforeEach(() => {
        updateWrapper();
        mockAxios.reset();
    });

    afterEach(() => {
        mockAxios.restore();
    });

    it("makes an API call when 'getData' method  called", async() => {
        updateWrapper();

        stubRequest();
        await wrapper.vm.getData()
        setTimeout(() => {
            expect(wrapper.vm.loading).toBe(false);
            expect(wrapper.vm.data).toEqual('fakeRequestData');
            expect(mockAxios.history.get[0].url).toBe("/api/admin/dashboarddropdown");
            done()
        }, 10)
    });


    it("makes `loading` as false when api returns error", async () => {
        updateWrapper();

        stubRequest(400);

        await wrapper.vm.getData();
        setTimeout(() => {
            expect(wrapper.vm.loading).toEqual(false)
            expect(wrapper.vm.data).toEqual('');
            expect(mockAxios.history.get[0].url).toBe("/api/admin/dashboarddropdown");
        }, 1);
    });
    function stubRequest(status = 200,url = '/api/admin/dashboarddropdown'){

        mockAxios.onGet(url).reply(status,fakeRequestData)

    }
})


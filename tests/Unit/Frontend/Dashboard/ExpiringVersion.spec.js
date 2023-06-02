import axios from "axios";
import MockAdapter from "axios-mock-adapter";
import { shallowMount } from "@vue/test-utils";
import ExpiringVersion from "../../../../resources/js/Pages/Dashboard/ExpiringVersion.vue";

describe("ExpiringVersion", () => {
    let mock;
    let wrapper;

    beforeEach(() => {
        mock = new MockAdapter(axios);
        wrapper = shallowMount(ExpiringVersion);
    });

    afterEach(() => {
        mock.reset();
    });

    it("should fetch and display data", async () => {
        const responseData = {
            data: {
                expired_versions: [
                    { version: "1.0", expiration_date: "2023-06-30" },
                    { version: "2.0", expiration_date: "2023-07-15" },
                ],
            },
        };

        mock.onGet("/api/admin/dashboarddropdown").reply(200, responseData);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual(responseData.data.expired_versions);
        expect(wrapper.find(".version").text()).toBe("1.0");
        expect(wrapper.find(".expiration_date").text()).toBe("2023-06-30");
    });

    it("should handle API error", async () => {
        mock.onGet("/api/admin/dashboarddropdown").reply(500);

        await wrapper.vm.getData();

        expect(wrapper.vm.data).toEqual([]);
    });
});

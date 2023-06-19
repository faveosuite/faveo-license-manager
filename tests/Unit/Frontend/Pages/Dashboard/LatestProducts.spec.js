import { shallowMount } from '@vue/test-utils';
import LatestProducts from "../../../../../resources/js/Pages/Dashboard/LatestProducts.vue";

describe('LatestVersion', () => {
    it('renders without errors', () => {
        const wrapper = shallowMount(LatestProducts);
        expect(wrapper.exists()).toBe(true);
    });
});

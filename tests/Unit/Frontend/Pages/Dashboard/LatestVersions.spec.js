import { shallowMount } from '@vue/test-utils';
import latestVersions from "../../../../../resources/js/Pages/Dashboard/LatestVersions.vue";
import axios from "axios";
import MockAdapter from "axios-mock-adapter";
describe('LatestVersion', () => {

    it('renders without errors', () => {
        const wrapper = shallowMount(latestVersions);
        expect(wrapper.exists()).toBe(true);
    });
    it('displays the correct card title', () => {
        const wrapper = shallowMount(latestVersions);
        const cardTitle = wrapper.find('.card-title');

        expect(cardTitle.text()).toBe('Latest Version');
    });

});

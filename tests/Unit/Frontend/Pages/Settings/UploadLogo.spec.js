import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import UploadLogo from "../../../../../resources/js/Pages/Settings/UploadLogo.vue";


jest.mock('axios', () => ({
    post: jest.fn(),
}));

describe('UploadLogo', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = shallowMount(UploadLogo);
    });

    afterEach(() => {
        jest.resetAllMocks();
    });

    it('renders the component properly', () => {
        expect(wrapper.exists()).toBe(true);
    });


    it('displays the translated value for the specified key', () => {
        // Set the language key
        const key = 'logo_name';

        // Call the lang method with the key
        const translatedValue = wrapper.vm.lang(key);

        // Assert that the translated value is correct
        expect(translatedValue).toBe(`Mock translated value for ${key}`);
    });
});

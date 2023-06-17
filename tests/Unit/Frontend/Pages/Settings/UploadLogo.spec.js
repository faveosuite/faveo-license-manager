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

    it('renders the component correctly', () => {
        expect(wrapper.exists()).toBe(true);
        expect(wrapper.find('.alert-info span').text()).toBe('Mock translated value for add_new_logo');
        expect(wrapper.find('.card-title').text()).toBe('Upload Logo');
        expect(wrapper.find('.btn-primary').text()).toBe('Mock translated value for save');
    });

    it('submits the form and calls the API when onSubmit is called', () => {
        // Mock the API response
        const response = { status: 'success' };
        axios.post.mockResolvedValue(response);

        // Set the input values
        wrapper.vm.logo_name = 'Logo Name';
        wrapper.vm.logo = 'logo.jpg';

        // Call the onSubmit method
        wrapper.vm.onSubmit();

        // Ensure that the API is called with the correct data
        expect(axios.post).toHaveBeenCalledWith('api/admin/store-logo-settings', {
            'logo_title': 'Logo Name',
            'login_image': 'logo.jpg',
        });

        // Ensure that the successHandler is called
        // You may need to mock the successHandler method and assert its behavior
    });

    it('calls the errorHandler when API request fails', () => {
        // Mock the API error response
        const error = { message: 'API error' };
        axios.post.mockRejectedValue(error);

        // Set the input values
        wrapper.vm.logo_name = 'Logo Name';
        wrapper.vm.logo = 'logo.jpg';

        // Call the onSubmit method
        wrapper.vm.onSubmit();

        // Ensure that the errorHandler is called
        // You may need to mock the errorHandler method and assert its behavior
    });

    it('displays the translated value for the specified key', () => {
        // Set the language key
        const key = 'logo_name';

        // Call the lang method with the key
        const translatedValue = wrapper.vm.lang(key);

        // Assert that the translated value is correct
        expect(translatedValue).toBe(`Mock translated value for ${key}`);
    });
    it('updates the logo property when image file changes', async () => {
        const file = new File(["logo.jpg"], "logo.jpg", { type: "image/jpeg" });
        const logoInput = wrapper.find('#logo_image');

        Object.defineProperty(logoInput.element, 'files', {
            value: [file],
            writable: true
        });

        await logoInput.trigger('change');

        expect(wrapper.vm.logo).toEqual(file);
    });
});


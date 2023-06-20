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
        const response = { status: 'success' };
        axios.post.mockResolvedValue(response);

        wrapper.vm.logo_name = 'Logo Name';
        wrapper.vm.logo = 'logo.jpg';

        wrapper.vm.onSubmit();

        expect(axios.post).toHaveBeenCalledWith('api/admin/store-logo-settings', {
            'logo_title': 'Logo Name',
            'login_image': 'logo.jpg',
        });
    });

    it('calls the errorHandler when API request fails', () => {
        const error = { message: 'API error' };
        axios.post.mockRejectedValue(error);

        wrapper.vm.logo_name = 'Logo Name';
        wrapper.vm.logo = 'logo.jpg';

        wrapper.vm.onSubmit();

    });

    it('displays the translated value for the specified key', () => {

        const key = 'logo_name';

        const translatedValue = wrapper.vm.lang(key);

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


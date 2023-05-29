import { shallowMount } from '@vue/test-utils';
import axios from 'axios';
import MockAdapter from 'axios-mock-adapter';
import UploadLogo from "../../../../../resources/js/Pages/Settings/UploadLogo.vue";

describe('UploadLogo', () => {
    let wrapper;
    let mockAxios;
    let alertSpy;

    beforeEach(() => {
        mockAxios = new MockAdapter(axios);
        wrapper = shallowMount(UploadLogo);
        alertSpy = jest.spyOn(window, 'alert').mockImplementation(() => {});
    });

    afterEach(() => {
        mockAxios.reset();
        alertSpy.mockRestore();
    });

    it('submits the form with correct data when onSubmit is called', async () => {
        // Check if the input elements are rendered
        const logoNameInput = wrapper.find('input#logo_name');
        const fileInput = wrapper.find('input#logo_image');

        if (logoNameInput.exists() && fileInput.exists()) {
            // Set the input values
            logoNameInput.element.value = 'Test Logo';
            fileInput.element.files = ['test_logo.png']; // Simulate file selection

            const mockResponse = { success: true };
            mockAxios.onPost('api/admin/store-logo-settings').reply(200, mockResponse);

            await wrapper.vm.onSubmit();

            expect(mockAxios.history.post.length).toBe(1);

            const postData = JSON.parse(mockAxios.history.post[0].data);
            expect(postData).not.toBeNull();
            expect(postData).toMatchObject({
                logo_title: 'Test Logo',
                login_image: 'test_logo.png',
            });

            expect(alertSpy).toHaveBeenCalledTimes(1);
            expect(alertSpy).toHaveBeenCalledWith('sent');
        } else {
            throw new Error('Logo name input or file input not found');
        }
    });

    it('updates the logo property when onImageChange is called', () => {
        // Check if the file input element is rendered
        const fileInput = wrapper.find('input#logo_image');

        if (fileInput.exists()) {
            const mockEvent = { target: { files: ['test_logo.png'] } };

            fileInput.trigger('change', mockEvent);

            expect(wrapper.vm.logo).toBe('test_logo.png');
        } else {
            throw new Error('File input not found');
        }
    });
});

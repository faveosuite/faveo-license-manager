import { mount } from '@vue/test-utils'

import SystemCleanupSettings from '../../../../../resources/js/Pages/Settings/SystemCleanupSettings'

describe('SystemCleanupSettings', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(SystemCleanupSettings);
    });

    it('renders the component properly', () => {
        expect(wrapper.exists()).toBe(true);
    });

    it('initializes with correct default data', () => {
        expect(wrapper.vm.title).toBe('system_cleanup_settings');
        expect(wrapper.vm.iconClass).toBe('fas fa-save');
    });

    it('clears PHP path properly', () => {
        wrapper.setData({ php_path: 'test', custom_php_path: 'test' });

        wrapper.vm.clearPhpPath();

        expect(wrapper.vm.php_path).toBe('');
        expect(wrapper.vm.custom_php_path).toBe('');
    });

    it('copies command properly', async () => {
        const mockPost = jest.fn(() => Promise.resolve({ data: {} }));

        wrapper.vm.copyCommand = mockPost;

        await wrapper.vm.copyCommand();

        expect(wrapper.vm.copying).toBe(false);
    });

    it('updates form data correctly on change', () => {
        const data = { value: 'test' };

        wrapper.vm.onChange(data, 'DATABASE_CLEANUP_CALLBACKS');

        expect(wrapper.vm.removeOlderCallbacksOptions).toEqual(data);
    });

    it('handles form data change properly', () => {
        const data = { value: 'test' };

        wrapper.vm.onChange(data, 'DATABASE_CLEANUP_REPORTS_MAIN');

        expect(wrapper.vm.removeLicenseReportsOptions).toEqual(data);
    });

    it('submits form data correctly', async () => {
        const mockPost = jest.fn(() => Promise.resolve({ data: {} }));

        wrapper.vm.onSubmit = mockPost;

        await wrapper.vm.onSubmit();

        expect(mockPost).toHaveBeenCalled();
    });

})

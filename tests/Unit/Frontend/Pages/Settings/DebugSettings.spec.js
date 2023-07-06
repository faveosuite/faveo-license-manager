import { shallowMount } from '@vue/test-utils';
import DebugSettings from "../../../../../resources/js/Pages/Settings/DebugSettings.vue";
import axios from 'axios';

jest.mock('axios');

describe('DebugSettings', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = shallowMount(DebugSettings);
        jest.useFakeTimers(); // Activate fake timers
    });

    afterEach(() => {
        wrapper.unmount();
        jest.useRealTimers(); // Restore real timers
    });

    it('renders the component properly', () => {
        expect(wrapper.exists()).toBe(true);
        expect(wrapper.find('.card-title').text()).toBe('Debugger Settings');
    });

    it('saves the selected debugger value', async () => {
        const response = { data: { debug: '1' } };
        axios.post.mockResolvedValueOnce(response);

        wrapper.setData({ selectedValue: '1' });
        await wrapper.vm.saveValue();

        expect(wrapper.vm.debugValue).toBe('1');
        expect(localStorage.getItem('debug')).toBe('1');
        expect(wrapper.vm.showLink).toBe(true);
        expect(localStorage.getItem('showLink')).toBe('true');

        expect(setTimeout).toHaveBeenCalledTimes(1);
        expect(setTimeout).toHaveBeenLastCalledWith(expect.any(Function), 500);

        // Fast-forward time
        jest.runAllTimers();

        // Assert any expectations after the setTimeout callback is invoked
        // For example, you can check if the page is reloaded
        expect(window.location.reload).toHaveBeenCalledTimes(1);
    });

    // Add more test cases for other scenarios as needed
});

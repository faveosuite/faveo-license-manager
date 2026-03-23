import { shallowMount } from '@vue/test-utils';
import MonitoringRedirectModal from "../../../../../resources/js/components/Reusable/MonitoringRedirectModal.vue";

const ModalStub = {
    name: 'modal',
    props: {
        showModal: { type: Boolean, default: false },
        onClose: { type: Function, default: () => {} },
        containerStyle: { type: Object, default: () => ({}) },
        showCloseBtn: { type: Boolean, default: true },
        showFooter: { type: Boolean, default: true },
    },
    template: `
        <div
            data-testid="modal"
            :data-show-modal="String(showModal)"
            :data-show-close-btn="String(showCloseBtn)"
            :data-show-footer="String(showFooter)"
        >
            <div data-testid="title"><slot name="title" /></div>
            <div data-testid="fields"><slot name="fields" /></div>
            <div data-testid="controls"><slot name="controls" /></div>
        </div>
    `,
};

function mountComponent(props = {}) {
    return shallowMount(MonitoringRedirectModal, {
        props: {
            showModal: true,
            onClose: () => {},
            ...props,
        },
        global: {
            stubs: {
                modal: ModalStub,
            },
        },
    });
}

describe('MonitoringRedirectModal.vue', () => {
    // The default content is mostly static text, so we check that it renders as expected.
    test('renders screenshot-like content for Pulse by default', () => {
        const wrapper = mountComponent();

        expect(wrapper.get('[data-testid="modal"]').attributes('data-show-footer')).toBe('false');
        expect(wrapper.text()).toContain('Monitoring unavailable');
        expect(wrapper.text()).toContain('Pulse could not load');
        expect(wrapper.text()).toContain('Invalid installation path detected.');
        expect(wrapper.text()).toContain('Folder-based installations are not supported.');
        expect(wrapper.text()).toContain('Example');
        expect(wrapper.text()).toContain('Not Supported');
        expect(wrapper.text()).toContain('Supported');
        expect(wrapper.text()).toContain('https://yourdomain.com/myapp');
        expect(wrapper.text()).toContain('https://yourdomain.com');
    });

    // The tool name should be used in the heading and body text, so we check that it replaces "Pulse" when a different tool is specified.
    test('uses tool prop in heading/body text', () => {
        const wrapper = mountComponent({ tool: 'Horizon' });
        expect(wrapper.text()).toContain('Horizon could not load');
        expect(wrapper.text()).toContain('because Horizon does not support folder-based installations');
    });

    // The modal should only render when `showModal` is true, so we check that it doesn't render when false.
    test('does not render modal when showModal is false', () => {
        const wrapper = shallowMount(MonitoringRedirectModal, {
            props: {
                showModal: false,
                onClose: () => {},
                tool: 'Pulse',
            },
            global: {
                stubs: {
                    modal: ModalStub,
                },
            },
        });

        expect(wrapper.find('[data-testid="modal"]').exists()).toBe(false);
    });

    // The instructions for resolving the issue are important content, so we check that all three bullet points render as expected.
    test('renders the three instruction bullet points', () => {
        const wrapper = mountComponent();
        const items = wrapper.findAll('li');

        expect(items).toHaveLength(3);
        expect(items[0].text()).toContain('Move the application to the web root');
        expect(items[1].text()).toContain('configure a separate subdomain');
        expect(items[2].text()).toContain('clear cache');
    });
});

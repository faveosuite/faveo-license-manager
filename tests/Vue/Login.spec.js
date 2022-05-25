// Import the `mount()` method from Vue Test Utils
import { mount } from '@vue/test-utils'
import CompAPI from '@vue/composition-api'

const localVue = createLocalVue()
localVue.use(CompAPI)

// The component to test
const MessageComponent = {
    template: '<p>{{ msg }}</p>',
    props: ['msg']
}

test('displays message', () => {
    // mount() returns a wrapped Vue component we can interact with
    const wrapper = mount(MessageComponent, {
        propsData: {
            msg: 'Hello world'
        },
        localVue
    })

    // Assert the rendered text of the component
    expect(wrapper.text()).toContain('Hello world')
})

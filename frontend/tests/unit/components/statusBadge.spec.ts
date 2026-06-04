import { mount } from '@vue/test-utils'
import { describe, expect, it } from 'vitest'
import StatusBadge from '../../../app/components/ui/molecules/StatusBadge.vue'

describe('StatusBadge', () => {
  it('renders label with success tone classes', () => {
    const wrapper = mount(StatusBadge, {
      props: {
        label: 'Ativo',
        tone: 'success',
      },
    })

    expect(wrapper.text()).toContain('Ativo')
    expect(wrapper.classes()).toContain('text-emerald-700')
  })

  it('uses neutral tone by default', () => {
    const wrapper = mount(StatusBadge, {
      props: {
        label: 'Manual',
      },
    })

    expect(wrapper.text()).toContain('Manual')
    expect(wrapper.classes()).toContain('text-secondary-foreground')
  })
})

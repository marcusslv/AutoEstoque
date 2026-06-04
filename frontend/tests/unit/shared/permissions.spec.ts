import { describe, expect, it } from 'vitest'
import { canAccess, canAccessAll, canAccessAny } from '../../../app/shared/permissions/permissions'

describe('permissions', () => {
  it('allows backoffice profiles to access management features', () => {
    expect(canAccess('owner', 'users')).toBe(true)
    expect(canAccess('manager', 'manual_inventory')).toBe(true)
    expect(canAccess('admin', 'catalog')).toBe(true)
  })

  it('keeps mechanic restricted to workshop and stock consultation', () => {
    expect(canAccess('mechanic', 'workshop')).toBe(true)
    expect(canAccess('mechanic', 'inventory')).toBe(true)
    expect(canAccess('mechanic', 'users')).toBe(false)
    expect(canAccess('mechanic', 'manual_inventory')).toBe(false)
  })

  it('denies access when role is missing', () => {
    expect(canAccess(null, 'dashboard')).toBe(false)
    expect(canAccess(undefined, 'dashboard')).toBe(false)
  })

  it('evaluates any/all permission helpers', () => {
    expect(canAccessAny('mechanic', ['users', 'workshop'])).toBe(true)
    expect(canAccessAll('admin', ['dashboard', 'catalog'])).toBe(true)
    expect(canAccessAll('mechanic', ['workshop', 'users'])).toBe(false)
  })
})

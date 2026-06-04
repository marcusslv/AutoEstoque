import { computed, ref } from 'vue'
import { afterEach, vi } from 'vitest'

type CookieStore = Record<string, ReturnType<typeof ref>>

const cookieStore: CookieStore = {}
const stateStore: CookieStore = {}
let nuxtAppMock: Record<string, unknown> = {}

Object.assign(globalThis, {
  ref,
  computed,
  useCookie: (name: string) => {
    cookieStore[name] ??= ref(null)

    return cookieStore[name]
  },
  useState: <T>(key: string, init: () => T) => {
    stateStore[key] ??= ref(init())

    return stateStore[key]
  },
  useNuxtApp: () => nuxtAppMock,
  navigateTo: vi.fn(),
})

export const setNuxtAppMock = (value: Record<string, unknown>) => {
  nuxtAppMock = value
}

export const resetNuxtTestState = () => {
  Object.keys(cookieStore).forEach((key) => {
    cookieStore[key].value = null
  })
  Object.keys(stateStore).forEach((key) => {
    stateStore[key].value = null
  })
  nuxtAppMock = {}
  vi.clearAllMocks()
}

afterEach(() => {
  resetNuxtTestState()
})

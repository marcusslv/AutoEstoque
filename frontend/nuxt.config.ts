export default defineNuxtConfig({
  compatibilityDate: '2026-06-02',
  modules: [
    '@pinia/nuxt',
    '@nuxtjs/tailwindcss',
  ],
  css: ['~/assets/css/tailwind.css'],
  runtimeConfig: {
    public: {
      apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL ?? 'http://localhost:8080/api/v1',
    },
  },
  typescript: {
    strict: true,
    typeCheck: true,
  },
  devtools: {
    enabled: true,
  },
})

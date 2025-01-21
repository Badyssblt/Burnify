// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  modules: ["@nuxt/ui", "@pinia/nuxt"],
  compatibilityDate: "2025-01-17",
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      API_URL: 'http://localhost:8215'
    }
  }
})
import { createI18n } from "vue-i18n"

const localesMessages = Object.fromEntries(
  Object.entries(
    import.meta.glob('../../../lang/*.json', { eager: true }))
    .map(([key, value]) => {
      const yaml = key.endsWith('.json')
      const index = key.lastIndexOf('/') + 1;
      return [key.slice(index, yaml ? -5 : -4), value.default]
    }),
)

const i18n = createI18n({
  locale: 'es',
  fallbackLocale: 'en',
  messages: localesMessages,
  legacy: false,
})

export default i18n;
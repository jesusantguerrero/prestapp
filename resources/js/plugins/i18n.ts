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

export const getLocales = () => {
  return Object.keys(import.meta.glob('../../../lang/*.json', { eager: true }))
  .map((itemPath) => {
    const yaml = itemPath.endsWith('.json')
    const index = itemPath.lastIndexOf('/') + 1;
    const name = itemPath.slice(index, yaml ? -5 : -4);

    return {
      id: name,
      name
    }
  });
}

export default i18n;
export const initWithLocale = (locale = 'es') => {
  const i18n = createI18n({
    locale,
    fallbackLocale: 'en',
    messages: localesMessages,
    legacy: false,
  })
  return i18n;
}

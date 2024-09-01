import "../resources/css/app.css"
import { initWithLocale} from '../resources/js/plugins/i18n.ts';
import { setup } from '@storybook/vue3';

setup((app) => {
  app.use(initWithLocale('en'));
  app.mixin({
    /* My mixin */
  });
});


/** @type { import('@storybook/vue3').Preview } */
const preview = {
  parameters: {
    actions: { argTypesRegex: "^on[A-Z].*" },
    controls: {
      matchers: {
        color: /(background|color)$/i,
        date: /Date$/,
      },
    },
  },
};

export default preview;

import './bootstrap';
import '../css/app.css';
import "atmosphere-ui/style.css"

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import VueApexCharts from 'vue3-apexcharts';
import VueMultiselect from 'vue-multiselect';
import { vRipple } from './utils/vRipple';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'
import { initWithLocale} from './plugins/i18n.ts';
import { es , enUS } from "date-fns/locale";
import setDefaultOptions from "date-fns/setDefaultOptions";
import { Locale } from 'date-fns';
import "vue-multiselect/dist/vue-multiselect.css";

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

const dateLocales: Record<string, Locale> = {
 es,
 en: enUS
}

createInertiaApp({
    progress: {
        color: '#4B5563'
    },
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
      // @ts-expect-error: will send this always
        const locale = props.initialPage.props?.userSettings?.region_language ?? "es";

        setDefaultOptions({
          locale: dateLocales[locale] ?? enUS
        })

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(ElementPlus)
            .use(initWithLocale(locale))
            .use(VueApexCharts)
            .use(autoAnimatePlugin)
            .component('multiselect', VueMultiselect)
            .directive('ripple', vRipple)
            .mount(el);
    },
});

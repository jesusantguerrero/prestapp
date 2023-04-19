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
import i18n from './plugins/i18n.ts';
import { supabase } from './plugins/supabase';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    progress: {
        color: '#4B5563'
    },
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(ElementPlus)
            .use(i18n)
            .use(VueApexCharts)
            .use(autoAnimatePlugin)
            .component('multiselect', VueMultiselect)
            .directive('ripple', vRipple)
            .provide('supabase', supabase)
            .mount(el);
    },
});

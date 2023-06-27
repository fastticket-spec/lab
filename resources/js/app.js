import {createApp, h} from 'vue'

import {createInertiaApp, Head, Link} from '@inertiajs/vue3'
import {InertiaProgress} from "@inertiajs/progress";
import BootstrapVue from 'bootstrap-vue';
import { createI18n } from "vue-i18n";

import Layout from "./Shared/Layout/Layout.vue";
import './Shared/components/directives'
import '../css/app.css';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import '../css/custom.css';
import '../css/PriceSlider.css';
import IQCard from './Shared/components/core/cards/iq-card.vue'
import ProgressBar from "./Shared/components/core/progressbar/ProgressBar.vue";
import NoData from './Shared/components/core/NoData/Index.vue';

import en from './Shared/components/lang/en.json'
import ar from './Shared/components/lang/ar.json'

createInertiaApp({
    resolve: async name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: false})
        let page = pages[`./Pages/${name}.vue`]
        let x = await page();
        x.default.layout = x.default.layout || Layout;
        return x;
    },
    setup({el, App, props, plugin}) {
        const i18n = createI18n({
            locale: localStorage.getItem('locale') || 'en', // user locale by props
            fallbackLocale: "en",
            messages: {en, ar},
            legacy: false
        });

        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(i18n)
            .use(BootstrapVue)
            .component('Link', Link)
            .component('Head', Head)
            .component('Progressbar', ProgressBar)
            .component('iq-card', IQCard)
            .component('no-data', NoData)
            .mount(el)
    },
    progress: {
        delay: 2000,
        color: '#29d',
        includeCSS: true,
        showSpinner: true
    }
});

InertiaProgress.init()

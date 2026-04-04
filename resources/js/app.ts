import '../css/app.css'
import './bootstrap'

import { createInertiaApp, type AppProps } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h, type DefineComponent } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura'; // 推薦使用 Aura 主題，比較現代

const appName: string = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title: string) => `${title} - ${appName}`,

    resolve: (name: string) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue')
        ),

    setup({ el, App, props, plugin }: AppProps) {
        return createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura // 設定主題預設值
                }
            })
            .mount(el as Element)
    },

    progress: {
        color: '#4B5563',
    },
})

import '../css/app.css'
import './bootstrap'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import { router } from '@inertiajs/vue3'

// import vue3-toastify
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)

        app.config.globalProperties.$toast = toast

        app.mount(el)
        return app
    },
    progress: {
        color: '#4B5563',
    },
})

let timeout = null

router.on('start', () => {
  clearTimeout(timeout)

  timeout = setTimeout(() => {
    document.dispatchEvent(new CustomEvent('loading:start'))
  }, 150)
})

// semua kondisi berhenti
const stopLoading = () => {
  clearTimeout(timeout)
  document.dispatchEvent(new CustomEvent('loading:finish'))
}

router.on('success', stopLoading)
router.on('error', stopLoading)
router.on('invalid', stopLoading)
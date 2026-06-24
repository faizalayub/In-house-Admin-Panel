import { createApp } from 'vue'
import PrimeVue from 'primevue/config'
import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice'
import Tooltip from 'primevue/tooltip'
import KeyFilter from 'primevue/keyfilter'
import router from './router/index.js'
import 'primeicons/primeicons.css'
import './css/app.css'
import App from './App.vue'

import Volt from './volt/index.js'

const app = createApp(App)
    .use(router)
    .use(PrimeVue, { unstyled: true })
    .use(ToastService)
    .use(ConfirmationService)
    .use(Volt)
    .directive('tooltip', Tooltip)
    .directive('keyfilter', KeyFilter)

app.mount('#app')

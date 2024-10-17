import { createApp } from 'vue'
import App from '@/App.vue'
import router from '@/router'
import store from '@/store'
import plugins from '@/plugins/plugins'
import Toast, { PluginOptions, POSITION } from 'vue-toastification'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.min.css'
import 'bootstrap'
import 'vue-toastification/dist/index.css'
import './assets/css/common.scss'

// Use Toastification.
const toastOptions: PluginOptions = {
  position: POSITION.TOP_RIGHT,
  timeout: 5000,
  closeOnClick: true
}

// Create the application.
const app = createApp(App)
  .use(store)
  .use(router)
  .use(Toast, toastOptions)
  .use(plugins)
  .component('LoadingSpinner', LoadingSpinner)

// Mount the application.
app.mount('#app')

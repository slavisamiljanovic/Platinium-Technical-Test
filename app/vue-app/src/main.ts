import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
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

createApp(App)
  .use(store)
  .use(router)
  .use(Toast, toastOptions)
  .component('LoadingSpinner', LoadingSpinner)
  .mount('#app')

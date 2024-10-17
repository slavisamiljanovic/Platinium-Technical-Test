import { App } from 'vue'
import { LoggerService } from '@/plugins/services/logger'

// Logger service.
const loggerService = new LoggerService()

export default {
  install (app: App) {
    // Logger service plugin.
    app.config.globalProperties.$logger = loggerService
  }
}

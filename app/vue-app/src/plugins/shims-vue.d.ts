// import { ComponentCustomProperties } from 'vue'
import { LoggerService } from '@/plugins/services/logger'

declare module '@vue/runtime-core' {
  // Declare a global property $logger on the component instance.
  interface ComponentCustomProperties {
    $logger: LoggerService
  }
}

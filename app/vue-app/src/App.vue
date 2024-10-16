<template>
  <div class="body-wrapper">
    <app-header />
    <div class="content-wrapper">
      <router-view/>
    </div>
    <app-footer />
  </div>
  <LoadingSpinner v-if="isLoading" :isLoading="isLoading" />
</template>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>

<script>
import { useHelper } from '@/store/helpers'
import AppHeader from '@/components/AppHeader.vue'
import AppFooter from '@/components/AppFooter.vue'

export default {
  name: 'App',
  components: {
    AppHeader,
    AppFooter
  },
  setup () {
    // Destructure methods from the helper.
    const {
      loaderSubscribe,
      loaderUnsubscribe
    } = useHelper()

    return {
      loaderSubscribe,
      loaderUnsubscribe
    }
  },
  mounted () {
    // Subscribe to loading state.
    this.loaderSubscribe((isLoading) => {
      if (process.env.NODE_ENV === 'development') {
        console.info('DEBUG-INFO: App -> loaderSubscribe(isLoading): ', isLoading)
      }
      this.isLoading = isLoading
    })
  },
  data () {
    return {
      isLoading: false
    }
  },
  beforeUnmount () {
    // Unsubscribe to avoid memory leaks.
    this.loaderUnsubscribe()
  }
}
</script>

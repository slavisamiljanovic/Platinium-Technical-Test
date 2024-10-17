<template>
  <footer>
    <p class="small-text mt-0 mb-0">Copyright &copy; {{ year }} | Technical Test - Tickets System</p>
  </footer>
</template>

<script>
import { computed } from 'vue'

export default {
  setup () {
    const year = computed(() => new Date().getFullYear())
    return { year }
  }
}
</script>

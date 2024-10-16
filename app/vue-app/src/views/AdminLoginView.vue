<template>
  <div class="home">
    <img src="../assets/logo.png" height="100" alt="Technical Test - Tickets System">
    <AdminLogin msg="Technical Test - Tickets System"/>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import AdminLogin from '@/components/AdminLogin.vue'

export default defineComponent({
  name: 'AdminLoginView',
  components: {
    AdminLogin
  }
})
</script>

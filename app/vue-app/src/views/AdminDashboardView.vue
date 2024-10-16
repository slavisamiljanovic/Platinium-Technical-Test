<template>
  <div class="home">
    <img src="../assets/logo.png" alt="Technical Test - Tickets System">
    <AdminDashboard />
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import AdminDashboard from '@/components/AdminDashboard.vue'

export default defineComponent({
  name: 'AdminDashboardView',
  components: {
    AdminDashboard
  }
})
</script>

<template>
  <div class="home">
    <TicketsList/>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import TicketsList from '@/components/tickets/TicketsList.vue'

export default defineComponent({
  name: 'TicketsListView',
  components: {
    TicketsList
  }
})
</script>

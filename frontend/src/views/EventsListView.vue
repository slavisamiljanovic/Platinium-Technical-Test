<template>
  <div class="home">
    <EventsList/>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import EventsList from '@/components/events/EventsList.vue'

export default defineComponent({
  name: 'EventsListView',
  components: {
    EventsList
  }
})
</script>

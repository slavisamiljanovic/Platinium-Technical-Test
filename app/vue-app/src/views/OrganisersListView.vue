<template>
  <div class="home">
    <OrganisersList/>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import OrganisersList from '@/components/organisers/OrganisersList.vue'

export default defineComponent({
  name: 'OrganisersListView',
  components: {
    OrganisersList
  }
})
</script>

<template>
  <div class="modal show d-block" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ ticket ? 'Edit Ticket' : 'Add Ticket' }}</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="save">
            <div class="mb-3">
              <label for="name" class="form-label">Ticket Name</label>
              <input type="text" class="form-control" id="name" v-model="formData.name" required />
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" v-model="formData.description" required></textarea>
            </div>
            <!-- Organiser Dropdown (Single select) -->
            <div class="mb-3">
              <label for="organiser" class="form-label">Organiser</label>
              <select class="form-control" v-model="formData.organiser_id" required>
                <option v-for="org in organisers" :key="org.id" :value="org.id">{{ org.name }}</option>
              </select>
            </div>
            <!-- Autocomplete Multiselect with Checkboxes (Vue Select) -->
            <div class="mb-3">
              <label for="tags" class="form-label">Tags (Multiselect with Autocomplete)</label>
              <v-select
                v-model="formData.selectedTags"
                :options="tags"
                label="name"
                multiple
                :close-on-select="false"
                :clear-on-select="false"
                :searchable="true"
                @search="onTagSearch"
                :reduce="tag => tag.id"
              />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
          <button type="button" class="btn btn-primary" @click="save">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select' // Import vue-select component
import 'vue-select/dist/vue-select.css' // Import vue-select styles

export default {
  components: {
    vSelect
  },
  props: {
    ticket: Object
  },
  data () {
    return {
      formData: {
        name: this.ticket?.name || '',
        description: this.ticket?.description || '',
        organiser_id: this.ticket?.organiser_id || '',
        selectedTags: this.ticket?.tags || []
      },
      organisers: [
        { id: 1, name: 'John Doe' },
        { id: 2, name: 'Jane Smith' }
        // Replace with your actual API data for organisers
      ],
      tags: [
        { id: 1, name: 'Music' },
        { id: 2, name: 'Festival' },
        { id: 3, name: 'Conference' }
        // Replace with actual tag data from your API
      ]
    }
  },
  methods: {
    save () {
      const ticket = {
        ...this.formData,
        organisers: this.organisers.find((org) => org.id === this.formData.organiser_id),
        tags: this.tags.filter((tag) => this.formData.selectedTags.includes(tag.id)),
        id: this.ticket?.id || null,
        created_at: this.ticket?.created_at || new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      this.$emit('save', ticket)
    },
    onTagSearch (searchTerm) {
      // Logic to search and update the tags, e.g., API call to fetch tags
      console.log('Searching for tags:', searchTerm)
    }
  }
}
</script>

<style scoped>
.modal.show {
  display: block;
}
</style>

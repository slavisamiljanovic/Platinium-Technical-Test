<template>
  <div class="modal fade show" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ ticket ? 'Edit Ticket' : 'Add Ticket' }}</h5>
          <button type="button" class="btn-close" @click.prevent="closeModal"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="save" class="form-group mt-3">
            <div class="mb-3">
              <label for="name" class="d-flex form-label required text-start small-text mb-1">Ticket Name</label>
              <input
                v-model="formData.name"
                type="text"
                class="form-control"
                id="name"
                placeholder="Ticket Name"
                required
                ref="nameInput"
              />
            </div>
            <div class="mb-3">
              <label for="description" class="d-flex form-label text-start small-text mb-1">Description</label>
              <textarea
                v-model="formData.description"
                class="form-control"
                id="description"
                placeholder="Description"
                rows="4"
              >
              </textarea>
            </div>
            <div class="mb-3">
              <label for="organiser" class="d-flex form-label required text-start small-text mb-1">Organiser</label>
              <select
                v-model="formData.organiser_id"
                class="form-control form-select"
                required
              >
                <option v-for="org in organisers" :key="org.id" :value="org.id">{{ org.name }}</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="events" class="d-flex form-label text-start small-text mb-1">Events</label>
              <v-select
                v-model="formData.selectedEvents"
                :options="events"
                label="name"
                multiple
                :close-on-select="false"
                :clear-on-select="false"
                :searchable="true"
                @search="onEventsSearch"
                :reduce="event => event.id"
              />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click.prevent="closeModal">Close</button>
          <button type="button" class="btn btn-primary" @click.prevent="saveTicket">Save</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
</template>

<script>
import { defineComponent } from 'vue'
// import { mapState } from 'vuex'
import vSelect from 'vue-select'
import { useHelper } from '@/store/helpers'
import 'vue-select/dist/vue-select.css'

export default defineComponent({
  components: {
    vSelect
  },
  emits: [
    'close',
    'save'
  ],
  props: {
    ticket: Object,
    eventsFeed: Object,
    organiserFeed: Object
  },
  setup () {
    // Destructure methods from the helper.
    const {
      isObjectEmpty
    } = useHelper()

    return {
      isObjectEmpty
    }
  },
  data () {
    return {
      events: [],
      eventsCount: 0,
      organisers: [],
      organisersCount: 0,
      formData: {
        name: this.ticket?.name || '',
        description: this.ticket?.description || '',
        organiser_id: this.ticket?.organiser_id || '',
        selectedEvents: this.ticket?.events || []
      },
      /*
      organisers: [
        { id: 1, name: 'John Doe' },
        { id: 2, name: 'Jane Smith' }
        // Replace with your actual API data for organisers
      ],
      */
      tags: [
        { id: 1, name: 'Music' },
        { id: 2, name: 'Festival' },
        { id: 3, name: 'Conference' }
        // Replace with actual tag data from your API
      ]
    }
  },
  mounted () {
    // Add event listener on mount.
    document.addEventListener('keydown', this.handleKeydown)
    document.body.classList.add('disable-scroll')

    // Focus the name input field when the component is mounted.
    this.$refs.nameInput.focus()

    if (!this.isObjectEmpty(this.$store.state.eventsFeed)) {
      this.events = this.$store.state.eventsFeed.events
      this.eventsCount = this.$store.state.eventsFeed.eventsCount
    }
    console.log('slavisa --> debug --> events: ', this.events)
    console.log('slavisa --> debug --> events: ', this.eventsCount)

    if (!this.isObjectEmpty(this.$store.state.organisersFeed)) {
      this.organisers = this.$store.state.organisersFeed.organisers
      this.organisersCount = this.$store.state.organisersFeed.organisersCount
    }
  },
  methods: {
    saveTicket () {
      /*
      const ticket = {
        ...this.formData,
        organisers: this.organisers.find((org) => org.id === this.formData.organiser_id),
        tags: this.tags.filter((tag) => this.formData.selectedEvents.includes(tag.id)),
        id: this.ticket?.id || null,
        created_at: this.ticket?.created_at || new Date().toISOString(),
        updated_at: new Date().toISOString()
      }
      this.$emit('save', ticket)
      */
    },
    onEventsSearch (searchTerm) {
      // Logic to search and update the tags, e.g., API call to fetch tags
      console.log('Searching for tags:', searchTerm)
    },
    closeModal () {
      this.$emit('close')
    },
    handleKeydown (event) {
      if (event.key === 'Escape') {
        this.closeModal()
      }
    },
    beforeUnmount () {
      // Clean up the event listener.
      document.removeEventListener('keydown', this.handleKeydown)
      document.body.classList.remove('disable-scroll')
    }
  }
  /*
  computed: {
    ...mapState(
      {
        eventsFeed: (state) => state.eventsFeed
      }
    )
  }
  */
})
</script>

<style scoped>
.modal.show {
  display: block;
}
</style>

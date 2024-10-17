<template>
  <div class="modal fade show" tabindex="-1" :class="{ 'd-block': !hideModal, 'd-none': hideModal }">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            {{ (modalAction === 'ADD') ? 'Add' : '' }}
            {{ ((modalAction === 'EDIT') ? 'Edit' : '') }}
            {{ (modalAction === 'DELETE') ? 'Delete' : '' }}
            Ticket
          </h5>
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
                @blur="validateName"
                :disabled="(modalAction === 'DELETE')"
              />
              <span class="error-placeholder">
                <span v-if="nameError" class="error text-danger">{{ nameError }}</span>
              </span>
            </div>
            <div class="mb-3">
              <label for="description" class="d-flex form-label text-start small-text mb-1">Description</label>
              <textarea
                v-model="formData.description"
                class="form-control"
                id="description"
                placeholder="Description"
                rows="6"
                :disabled="(modalAction === 'DELETE')"
              >
              </textarea>
            </div>
            <div class="mb-3">
              <label for="events" class="d-flex form-label text-start small-text mb-1">Events</label>
              <v-select
                v-model="formData.selectedEvents"
                placeholder="Select events"
                label="name"
                :options="events"
                :get-option-label="(option) => customLabel(option)"
                :close-on-select="false"
                :clear-on-select="false"
                :searchable="true"
                :disabled="(modalAction === 'DELETE')"
                multiple
              ></v-select>
              <!--
              @search="onEventsSearch"
              :reduce="event => event.id"
              -->
            </div>
            <span class="error-placeholder response-error mt-0 mb-4">
              <span v-if="responseError" class="error text-danger">{{ responseError }}</span>
            </span>
          </form>
        </div>
        <div class="modal-footer">
          <p v-if="(modalAction === 'DELETE')" class="text-danger">Are you sure you want to delete this ticket?</p>
          <button type="button" class="btn btn-secondary" @click.prevent="closeModal">Close</button>
          <button
            type="button"
            class="btn"
            :class="{ 'btn-primary': (modalAction !== 'DELETE'), 'btn-danger': (modalAction === 'DELETE') }"
            @click.prevent="dispatchAction"
          >
            {{ (modalAction === 'DELETE') ? 'Delete' : 'Save' }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
</template>

<script>
import { defineComponent } from 'vue'
import vSelect from 'vue-select'
import { useHelper } from '@/store/helpers'
import 'vue-select/dist/vue-select.css'

export default defineComponent({
  components: {
    vSelect
  },
  emits: [
    'close',
    'dispatchAction'
  ],
  props: {
    ticket: Object,
    eventsFeed: Object,
    organiserFeed: Object,
    modalAction: String,
    hideModal: Boolean,
    responseError: String
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
  computed: {
    isFormValid () {
      return !this.nameError
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
        selectedEvents: this.ticket?.events || []
      },
      nameError: ''
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

    if (!this.isObjectEmpty(this.$store.state.organisersFeed)) {
      this.organisers = this.$store.state.organisersFeed.organisers
      this.organisersCount = this.$store.state.organisersFeed.organisersCount
    }
  },
  methods: {
    validateName () {
      this.nameError = this.formData.name !== ''
        ? ''
        : 'Ticket name is required field.'
    },
    dispatchAction () {
      // Optionally, perform additional validation before submitting.
      this.validateName()
      if (!this.isFormValid) {
        return
      }

      const ticket = {
        ...this.formData,
        events: []
        // @todo-Slavisa
        // events: this.events.find((event) => event.id === this.formData.id)
        // organisers: this.organisers.find((org) => org.id === this.formData.organiser_id),
        // tags: this.tags.filter((tag) => this.formData.selectedEvents.includes(tag.id)),
      }
      if (this.formData.selectedEvents.length > 0) {
        const events = this.formData.selectedEvents.map(
          ({ id, name }) => ({ id, name })
        )
        ticket.events = events
      }
      delete ticket.selectedEvents
      this.$emit('dispatchAction', ticket, this.ticket?.id)
    },
    customLabel (option) {
      return `[${option.isActive ? 'Active' : 'Inactive'}]: ${option.name}`
    },
    /*
    onEventsSearch (searchTerm) {
      // Logic to search and update the events, e.g., API call to fetch tags.
      // console.log('Searching for tags:', searchTerm)
    },
    */
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
})
</script>

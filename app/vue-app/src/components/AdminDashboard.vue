<template>
  <div class="container">
    <h1 class="mt-5">Admin Dashboard</h1>
    <div class="row">
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Manage Tickets <span v-if="ticketsCount !== null">({{ ticketsCount }})</span></h5>
            <p class="card-text">Create, edit, or delete tickets.</p>
            <router-link to="/tickets/1" class="btn btn-primary">Go to Tickets</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Manage Events <span v-if="eventsCount !== null">({{ eventsCount }})</span></h5>
            <p class="card-text">Create, edit, or delete events.</p>
            <router-link to="/events" class="btn btn-primary">Go to Events</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Manage Organisers <span v-if="organisersCount !== null">({{ organisersCount }})</span></h5>
            <p class="card-text">Create, edit, or delete organisers.</p>
            <router-link to="/organisers" class="btn btn-primary">Go to Organisers</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { useToast } from 'vue-toastification'
import { useHelper } from '@/store/helpers'
import {
  LIST_LIMIT,
  LIST_OFFSET
} from '@/store/constants'

export default {
  name: 'AdminDashboard',
  setup () {
    // Get toast interface.
    const toast = useToast()

    // Destructure methods from the helper.
    const { handleApiError } = useHelper()

    // Make it available inside methods.
    return {
      toast,
      handleApiError
    }
  },
  mounted () {
    this.fetchTickets()
    this.fetchEvents()
    this.fetchOrganisers()
  },
  data () {
    return {
      tickets: [],
      ticketsCount: null,
      events: [],
      eventsCount: null,
      organisers: [],
      organisersCount: null
    }
  },
  methods: {
    fetchTickets () {
      // Request params.
      const requestParams = {
        limit: LIST_LIMIT,
        offset: LIST_OFFSET
      }
      this.$logger.log('AdminDashboard -> fetchTickets() -> params', { logType: 'info', logData: requestParams })
      this.$store.dispatch('fetchTickets', requestParams)
        .then(
          (response) => {
            this.tickets = response.data
            this.ticketsCount = this.tickets.ticketsCount
            this.toast.info('Tickets list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          this.$logger.log('AdminDashboard -> fetchTickets() -> error', { logType: 'error', logData: error })
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of tickets.'))
        })
    },
    fetchEvents () {
      // Request params.
      const requestParams = {
        limit: LIST_LIMIT,
        offset: LIST_OFFSET
      }
      this.$logger.log('AdminDashboard -> fetchEvents() -> params', { logType: 'info', logData: requestParams })
      this.$store.dispatch('fetchEvents', requestParams)
        .then(
          (response) => {
            this.events = response.data
            this.eventsCount = this.events.eventsCount
            this.toast.info('Events list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          this.$logger.log('AdminDashboard -> fetchEvents() -> error', { logType: 'error', logData: error })
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of events.'))
        })
    },
    fetchOrganisers () {
      // Request params.
      const requestParams = {
        limit: LIST_LIMIT,
        offset: LIST_OFFSET
      }
      this.$logger.log('AdminDashboard -> fetchOrganisers() -> params', { logType: 'info', logData: requestParams })
      this.$store.dispatch('fetchOrganisers', requestParams)
        .then(
          (response) => {
            this.organisers = response.data
            this.organisersCount = this.organisers.organisersCount
            this.toast.info('Organisers list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          this.$logger.log('AdminDashboard -> fetchOrganisers() -> error', { logType: 'error', logData: error })
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of organisers.'))
        })
    }
  }
}
</script>

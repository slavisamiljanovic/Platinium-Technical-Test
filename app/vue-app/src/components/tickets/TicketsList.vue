<template>
  <div class="list-wrapper">
    <h1 class="mt-2">Tickets List</h1>

    <!-- Pagination controls. -->
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a href="#" class="page-link" @click.prevent="goToPage(currentPage - 1)">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
          <a href="#" class="page-link" @click.prevent="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a href="#" class="page-link" @click.prevent="goToPage(currentPage + 1)">Next</a>
        </li>
        <li class="page-item last-item">
          <button @click.prevent="openModal(null)" class="btn btn-primary me-2">Add Ticket</button>
          <button @click.prevent="openModal(null)" class="btn btn-primary me-2">Add Event</button>
          <button @click.prevent="openModal(null)" class="btn btn-primary">Add Organiser</button>
        </li>
      </ul>
    </nav>

    <table class="table mt-3 table-list">
      <thead>
        <tr>
          <th class="id text-start"><a href="#" @click.prevent="sortBy('id')">ID <i :class="getSortClass('id')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('name')">Name <i :class="getSortClass('name')"></i></a></th>
          <th class="description text-start"><a href="#" @click.prevent="sortBy('description')">Description <i :class="getSortClass('description')"></i></a></th>
          <th class="text-center ticket-events">Events</th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('created_at')">Created At <i :class="getSortClass('created_at')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('updated_at')">Updated At <i :class="getSortClass('updated_at')"></i></a></th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(ticket, index) in paginatedTickets"
          :key="ticket.id"
          :class="{'odd': helperIsOdd(index), 'even': !helperIsOdd(index)}"
        >
          <td class="id text-start">{{ ticket.id }}</td>
          <td class="text-start">{{ ticket.name }}</td>
          <td class="description text-start">{{ ticket.description }}</td>
          <td class="text-center ticket-events">
            <button v-if="ticket.events.length > 0" @click.prevent="openModal(ticket, true)" class="btn btn-sm btn-secondary">View Events</button>
            <span v-if="ticket.events.length === 0" class="text-danger">No ticket-related events.</span>
          </td>
          <td class="text-end">{{ helperFormatDateTime(new Date(ticket.createdAt)) }}</td>
          <td class="text-end">{{ helperFormatDateTime(new Date(ticket.updatedAt)) }}</td>
          <td class="text-center">
            <button @click.prevent="openModal(ticket)" class="btn btn-sm btn-secondary me-2">Edit</button>
            <button @click.prevent="deleteEvent(ticket.id)" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedTickets.length === 0">
          <td class="text-center" colspan="7">
            <span class="text-danger">Currently, there are no tickets listed.</span>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination controls. -->
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a href="#" class="page-link" @click.prevent="goToPage(currentPage - 1)">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
          <a href="#" class="page-link" @click.prevent="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a href="#" class="page-link" @click.prevent="goToPage(currentPage + 1)">Next</a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Modal to view ticket-related events. -->
  <EventsModal
    v-if="isEventsModalOpen"
    :ticket="selectedTicket"
    @close="isEventsModalOpen = false"
  />

  <!-- Modal for adding / editing tickets. -->
  <TicketModal
    v-if="isTicketModalOpen"
    :ticket="selectedTicket"
    :eventsFeed="eventsFeed"
    :organiserFeed="organiserFeed"
    @close="isTicketModalOpen = false"
    @save="saveEvent"
  />
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'
import { useHelper } from '@/store/helpers'
import { LIST_LIMIT, LIST_OFFSET } from '@/store/constants'
import TicketModal from './TicketModal.vue'
import EventsModal from '@/components/events/EventsModal.vue'

export default {
  components: {
    TicketModal,
    EventsModal
  },
  setup () {
    // Get toast interface.
    const toast = useToast()

    // Destructure methods from the helper.
    const {
      helperIsOdd,
      helperFormatDateTime,
      handleApiError
    } = useHelper()

    return {
      toast,
      helperIsOdd,
      helperFormatDateTime,
      handleApiError
    }
  },
  mounted () {
    // Current page based on route parameter.
    this.currentPage = Number(this.$route.params.currentPage) || 1

    // Fetch the paginated tickets list.
    this.fetchTickets(this.perPage, (this.currentPage - 1) * this.perPage)

    // Fetch and store all events in the Vuex store.
    this.fetchEventsFeed()

    // Fetch and store all organisers in the Vuex store.
    this.fetchOrganisersFeed()
  },
  data () {
    return {
      tickets: [],
      ticketsCount: 0,
      selectedTicket: null,
      eventsFeed: null,
      organiserFeed: null,
      isEventsModalOpen: false,
      isTicketModalOpen: false,
      sortKey: 'id', // Current column to sort by.
      sortOrder: 'asc', // Sort order: 'asc' or 'desc'.
      currentPage: 1, // Current page number.
      perPage: LIST_LIMIT // Number of items per page.
    }
  },
  methods: {
    fetchTickets (limit, offset) {
      limit = limit || LIST_LIMIT
      offset = offset || LIST_OFFSET

      // Request params.
      const requestParams = {
        limit,
        offset
      }
      this.$store.dispatch('fetchTickets', requestParams)
        .then(
          (response) => {
            this.tickets = response.data.tickets
            this.ticketsCount = response.data.ticketsCount
            // this.toast.info('Tickets list fetched successfully.')
            const currentPage = this.currentPage
            this.$router.push({ name: 'tickets', params: { currentPage } })
          }
        )
        .catch(error => {
          // Handle error response.
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of tickets.'))
        })
    },
    fetchTicket (ticketId) {
      this.$store.dispatch('fetchTicket', ticketId)
        .then(
          (response) => {
            const ticket = response.data.ticket
            this.selectedTicket = { ...ticket }
            // this.toast.info('Ticket fetched successfully.')
            this.isEventsModalOpen = true
          }
        )
        .catch(error => {
          // Handle error response.
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of tickets.'))
        })
    },
    fetchEventsFeed () {
      this.$store.dispatch('fetchEventsFeed')
        .then(
          () => {
            // this.toast.info('Events feed list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          this.toast.error(this.handleApiError(error, 'Failed to fetch the events feed list.'))
        })
    },
    fetchOrganisersFeed () {
      this.$store.dispatch('fetchOrganisersFeed')
        .then(
          () => {
            // this.toast.info('Events feed list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          this.toast.error(this.handleApiError(error, 'Failed to fetch the organisers feed list.'))
        })
    },
    openModal (ticket, viewEvents) {
      // Clone the ticket or set to NULL for NEW.
      this.selectedTicket = ticket ? { ...ticket } : null
      if (viewEvents) {
        // Fetch entire ticket.
        this.fetchTicket(this.selectedTicket.id)
      } else {
        this.isTicketModalOpen = true
      }
    },
    saveEvent (ticket) {
      console.log('slavisa --> debug --> saveEvent (ticket): ', ticket)
      if (ticket.id) {
        // Edit existing ticket
        const index = this.tickets.findIndex((e) => e.id === ticket.id)
        if (index !== -1) {
          this.$set(this.tickets, index, ticket)
        }
      } else {
        // Add new ticket
        ticket.id = this.tickets.length + 1 // Temporary ID, adjust with real API logic
        this.tickets.push(ticket)
      }
      this.isTicketModalOpen = false
    },
    async deleteEvent (eventId) {
      const confirmed = confirm('Are you sure you want to delete this ticket?')
      if (confirmed) {
        try {
          this.tickets = this.tickets.filter((ticket) => ticket.id !== eventId)
          await axios.delete(`/api/tickets/${eventId}`) // Replace with your actual API endpoint
          this.fetchEvents() // Refresh the tickets list after deletion
        } catch (error) {
          console.error('Error deleting ticket:', error)
        }
      }
    },
    sortBy (column) {
      if (this.sortKey === column) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc'
      } else {
        this.sortKey = column
        this.sortOrder = 'asc'
      }
    },
    getSortClass (column) {
      if (this.sortKey === column) {
        return this.sortOrder === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'
      }
      return ''
    },
    goToPage (page) {
      if (page > 0 && page <= this.totalPages) {
        this.currentPage = page
        this.fetchTickets(this.perPage, (page - 1) * this.perPage)
      }
    }
  },
  computed: {
    sortedTickets () {
      const sorted = [...this.tickets].sort((a, b) => {
        const modifier = this.sortOrder === 'asc' ? 1 : -1
        /*
        if (this.sortKey === 'events') {
          return (
            (a[this.sortKey].length - b[this.sortKey].length) * modifier
          )
        }
        */
        if (a[this.sortKey] < b[this.sortKey]) return -1 * modifier
        if (a[this.sortKey] > b[this.sortKey]) return 1 * modifier
        return 0
      })
      return sorted
    },
    paginatedTickets () {
      /*
      const start = (this.currentPage - 1) * this.perPage
      const end = start + this.perPage
      return this.sortedTickets.slice(start, end)
      */
      return this.sortedTickets
    },
    totalPages () {
      return Math.ceil(this.ticketsCount / this.perPage)
    }
  },
  beforeUnmount () {
    // Unsubscribe to avoid memory leaks.
  }
}
</script>

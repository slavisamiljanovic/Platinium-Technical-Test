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
          <th class="text-end"><a href="#" @click.prevent="sortBy('createdAt')">Created At <i :class="getSortClass('createdAt')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('updatedAt')">Updated At <i :class="getSortClass('updatedAt')"></i></a></th>
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
    :data="selectedData"
    @close="isEventsModalOpen = false"
  />

  <!-- Modal for adding / editing ticket. -->
  <TicketModal
    v-if="isTicketModalOpen"
    :data="selectedData"
    @close="isTicketModalOpen = false"
    @save="saveEvent"
  />
</template>

<script>
import { watch, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
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
    // Access Vuex store.
    const store = useStore()

    // Access Vue route.
    const route = useRoute()

    // Access Vue router.
    const router = useRouter()

    // Get toast interface.
    const toast = useToast()

    // Destructure methods from the helper.
    const {
      helperIsOdd,
      helperFormatDateTime,
      handleApiError
    } = useHelper()

    const tickets = ref([])
    const ticketsCount = ref(0)

    const selectedData = ref(null)
    const isEventsModalOpen = ref(false)
    const isTicketModalOpen = ref(false)

    const currentPage = ref(1) // Default value for current page number.
    const perPage = LIST_LIMIT // Number of items per page.
    const sortKey = ref('updatedAt') // Current column to sort by.
    const sortOrder = ref('desc') // Sort order: 'asc' or 'desc'.

    /**
     * Watchers: Watch for route query changes.
     */
    watch(() => route.params.currentPage, (newPage) => {
      fetchTickets(perPage, (newPage - 1) * perPage)
    })

    /**
     * Regular Methods.
     */
    const fetchTickets = async (limit, offset) => {
      limit = limit || LIST_LIMIT
      offset = offset || LIST_OFFSET

      // Request params.
      const requestParams = {
        limit,
        offset
      }
      await store.dispatch('fetchTickets', requestParams)
        .then(
          (response) => {
            tickets.value = response.data.tickets
            ticketsCount.value = response.data.ticketsCount
            // toast.info('Tickets list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          toast.error(handleApiError(error, 'Failed to fetch the list of tickets.'))
        })
    }

    const fetchTicket = (ticketId, viewEvents) => {
      store.dispatch('fetchTicket', ticketId)
        .then(
          (response) => {
            const ticket = response.data.ticket
            selectedData.value = { ...ticket }
            // this.toast.info('Ticket fetched successfully.')
            if (viewEvents) {
              isEventsModalOpen.value = true
            } else {
              isTicketModalOpen.value = true
            }
          }
        )
        .catch(error => {
          // Handle error response.
          this.toast.error(this.handleApiError(error, 'Failed to fetch the list of tickets.'))
        })
    }

    const openModal = (ticket, viewEvents) => {
      // Clone the ticket or set to NULL for NEW.
      selectedData.value = ticket ? { ...ticket } : null
      // Fetch entire ticket.
      fetchTicket(selectedData.value.id, viewEvents)
    }

    const fetchEventsFeed = () => {
      store.dispatch('fetchEventsFeed')
        .then(
          () => {
            // toast.info('Events feed list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          toast.error(this.handleApiError(error, 'Failed to fetch the events feed list.'))
        })
    }

    const fetchOrganisersFeed = () => {
      store.dispatch('fetchOrganisersFeed')
        .then(
          () => {
            // toast.info('Events feed list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          toast.error(this.handleApiError(error, 'Failed to fetch the organisers feed list.'))
        })
    }

    const sortBy = (column) => {
      if (sortKey.value === column) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
      } else {
        sortKey.value = column
        sortOrder.value = 'asc'
      }
    }

    const getSortClass = (column) => {
      if (sortKey.value === column) {
        return sortOrder.value === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'
      }
      return ''
    }

    const goToPage = (page) => {
      if (page > 0 && page <= totalPages.value) {
        currentPage.value = page
        router.push({ name: 'tickets', params: { currentPage: currentPage.value } })
      }
    }

    /**
     * Computed methods.
     */
    const sortedTickets = computed(() => {
      const sorted = [...tickets.value].sort((a, b) => {
        const modifier = sortOrder.value === 'asc' ? 1 : -1
        /*
        if (this.sortKey === 'events') {
          return (
            (a[this.sortKey].length - b[this.sortKey].length) * modifier
          )
        }
        */
        if (a[sortKey.value] < b[sortKey.value]) return -1 * modifier
        if (a[sortKey.value] > b[sortKey.value]) return 1 * modifier
        return 0
      })
      return sorted
    })

    const paginatedTickets = computed(() => {
      /*
      const start = (this.currentPage - 1) * this.perPage
      const end = start + this.perPage
      return this.sortedTickets.slice(start, end)
      */
      return sortedTickets.value
    })

    const totalPages = computed(() => {
      return Math.ceil(ticketsCount.value / perPage)
    })

    /**
     * onMounted lifecycle hook: Executes when the component is mounted.
     */
    onMounted(() => {
      // Current page based on route parameter.
      currentPage.value = Number(route.params.currentPage) || 1

      // Fetch the paginated tickets list.
      fetchTickets(perPage, (currentPage.value - 1) * perPage)

      // Fetch and store all events in the Vuex store.
      fetchEventsFeed()

      // Fetch and store all organisers in the Vuex store.
      fetchOrganisersFeed()
    })

    return {
      toast,
      tickets,
      ticketsCount,
      currentPage,
      perPage,
      sortKey,
      sortOrder,
      paginatedTickets,
      totalPages,
      sortBy,
      getSortClass,
      goToPage,
      selectedData,
      isEventsModalOpen,
      isTicketModalOpen,
      helperIsOdd,
      helperFormatDateTime,
      handleApiError,
      fetchTickets,
      fetchEventsFeed,
      fetchOrganisersFeed,
      openModal
    }
  }
  /*
  methods: {
    saveEvent (ticket) {
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
    }
  }
  */
}
</script>

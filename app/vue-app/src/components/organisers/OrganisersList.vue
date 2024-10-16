<template>
  <div>
    <h1 class="mt-5">Tickets List</h1>
    <button @click="openModal(null)" class="btn btn-primary">Add Ticket</button>
    <table class="table mt-3">
      <thead>
        <tr>
          <th @click="sortBy('id')">ID <i :class="getSortClass('id')"></i></th>
          <th @click="sortBy('name')">Name <i :class="getSortClass('name')"></i></th>
          <th @click="sortBy('description')">Description <i :class="getSortClass('description')"></i></th>
          <th @click="sortBy('organiser_id')">Organiser <i :class="getSortClass('organiser_id')"></i></th>
          <th @click="sortBy('tags')">Tags <i :class="getSortClass('tags')"></i></th>
          <th @click="sortBy('created_at')">Created At <i :class="getSortClass('created_at')"></i></th>
          <th @click="sortBy('updated_at')">Updated At <i :class="getSortClass('updated_at')"></i></th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ticket in paginatedEvents" :key="ticket.id">
          <td>{{ ticket.id }}</td>
          <td>{{ ticket.name }}</td>
          <td>{{ ticket.description }}</td>
          <td>
            <ul>
              <li v-for="org in ticket.organisers" :key="org.id">{{ org.name }}</li>
            </ul>
          </td>
          <td>
            <ul>
              <li v-for="tag in ticket.tags" :key="tag.id">{{ tag.name }}</li>
            </ul>
          </td>
          <td>{{ ticket.created_at }}</td>
          <td>{{ ticket.updated_at }}</td>
          <td>
            <button @click="openModal(ticket)" class="btn btn-sm btn-secondary">Edit</button>
            <button @click="deleteEvent(ticket.id)" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a class="page-link" @click="goToPage(currentPage - 1)">Previous</a>
        </li>
        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
          <a class="page-link" @click="goToPage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <a class="page-link" @click="goToPage(currentPage + 1)">Next</a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- Modal for adding / editing tickets -->
  <TicketModal
    v-if="isModalOpen"
    :ticket="selectedTicket"
    @close="isModalOpen = false"
    @save="saveEvent"
  />
</template>

<script>
import axios from 'axios'
import TicketModal from './TicketModal.vue'
import helpers from '@/store/helpers'

export default {
  components: {
    TicketModal
  },
  // Call these methods in mounted
  async mounted () {
    // Subscribe to loading state
    helpers.loaderSubscribe((isLoading) => {
      console.log('slavisa --> debug --> helpers.loaderSubscribe((isLoading): ', isLoading)
      this.isLoading = isLoading
    })
    await this.fetchEvents()
    await this.fetchOrganizers()
    await this.fetchTags()
  },
  data () {
    return {
      tickets: [
        {
          id: 1,
          name: 'Music Festival',
          description: 'Outdoor concert',
          organiser_id: 1,
          tags: [
            {
              id: 1,
              name: 'Music'
            },
            {
              id: 2,
              name: 'Festival'
            }
          ],
          created_at: '2023-10-10',
          updated_at: '2023-10-11'
        },
        {
          id: 2,
          name: 'Tech Conference',
          description: 'Industry insights',
          organiser_id: 2,
          tags: [
            {
              id: 3,
              name: 'Conference'
            }
          ],
          created_at: '2023-10-12',
          updated_at: '2023-10-13'
        }
        // More tickets...
      ],
      organisers: [],
      tags: [],
      isModalOpen: false,
      selectedEvent: null,
      sortKey: '', // Current column to sort by.
      sortOrder: 'asc', // Sort order: 'asc' or 'desc'.
      currentPage: 1, // Current page number.
      perPage: 50, // Number of items per page.
      isLoading: true
    }
  },
  methods: {
    openModal (ticket) {
      this.selectedEvent = ticket ? { ...ticket } : null // Clone the ticket or set to null for new
      this.isModalOpen = true
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
      this.isModalOpen = false
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
      console.log('slavisa --> debug: sortBy (column): ', column)
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
      }
    },
    async fetchEvents () {
      helpers.loaderStartLoading() // Start loading
      try {
        const response = await axios.get('/api/tickets') // Replace with your actual API endpoint
        this.tickets = response.data // Assuming the API returns an array of tickets
      } catch (error) {
        console.error('Error fetching tickets:', error)
      } finally {
        helpers.loaderStopLoading() // End loading
      }
    },
    async fetchOrganizers () {
      helpers.loaderStartLoading() // Start loading
      try {
        const response = await axios.get('/api/organisers') // Replace with your actual API endpoint
        this.organisers = response.data // Assuming the API returns an array of organisers
      } catch (error) {
        console.error('Error fetching organisers:', error)
      } finally {
        helpers.loaderStopLoading() // End loading
      }
    },
    async fetchTags () {
      helpers.loaderStartLoading() // Start loading
      try {
        const response = await axios.get('/api/tags') // Replace with your actual API endpoint
        this.tags = response.data // Assuming the API returns an array of tags
      } catch (error) {
        console.error('Error fetching tags:', error)
      } finally {
        helpers.loaderStopLoading() // End loading
      }
    }
  },
  computed: {
    sortedEvents () {
      console.log('slavisa --> debug --> sortedEvents ()')
      const sorted = [...this.tickets].sort((a, b) => {
        const modifier = this.sortOrder === 'asc' ? 1 : -1
        if (this.sortKey === 'tags') {
          return (
            (a[this.sortKey].length - b[this.sortKey].length) * modifier
          )
        }
        if (a[this.sortKey] < b[this.sortKey]) return -1 * modifier
        if (a[this.sortKey] > b[this.sortKey]) return 1 * modifier
        return 0
      })
      return sorted
    },
    paginatedEvents () {
      const start = (this.currentPage - 1) * this.perPage
      const end = start + this.perPage
      return this.sortedEvents.slice(start, end)
    },
    totalPages () {
      return Math.ceil(this.tickets.length / this.perPage)
    }
  },
  beforeUnmount () {
    // Unsubscribe to avoid memory leaks
    helpers.loaderUnsubscribe()
  }
}
</script>

<style scoped>
.table {
  width: 100%;
}
/* Optional sorting icons styling (Bootstrap Icons) */
/*
.bi-sort-up::before {
  content: "\f0de";
}
.bi-sort-down::before {
  content: "\f0dd";
}
*/
</style>

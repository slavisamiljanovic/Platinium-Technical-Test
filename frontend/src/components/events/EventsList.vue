<template>
  <div class="list-wrapper">
    <h1 class="mt-2">Events List</h1>

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
          <button @click.prevent="openModal(null, 'ADD_EVENT')" class="btn btn-primary">Add Event</button>
        </li>
      </ul>
    </nav>

    <table class="table mt-3 table-list">
      <thead>
        <tr>
          <th class="id text-start"><a href="#" @click.prevent="sortBy('id')">ID <i :class="getSortClass('id')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('name')">Name <i :class="getSortClass('name')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('organiser')">Organiser <i :class="getSortClass('organiser')"></i></a></th>
          <th class="description text-start"><a href="#" @click.prevent="sortBy('description')">Description <i :class="getSortClass('description')"></i></a></th>
          <th class="text-center"><a href="#" @click.prevent="sortBy('active')">Active <i :class="getSortClass('active')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('createdAt')">Created At <i :class="getSortClass('createdAt')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('updatedAt')">Updated At <i :class="getSortClass('updatedAt')"></i></a></th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(event, index) in paginatedEvents"
          :key="event.id"
          :class="{'odd': helperIsOdd(index), 'even': !helperIsOdd(index)}"
        >
          <td class="id text-start">{{ event.id }}</td>
          <td class="text-start">{{ event.name }}</td>
          <td class="text-start">{{ event.organiser.name }}</td>
          <td class="description text-start">{{ event.description }}</td>
          <td class="text-center">
            <span :class="{'text-success': event.isActive, 'text-danger': !event.isActive}">{{ event.isActive ? 'Yes' : 'No' }}</span>
          </td>
          <td class="text-end">{{ helperFormatDateTime(new Date(event.createdAt)) }}</td>
          <td class="text-end">{{ helperFormatDateTime(new Date(event.updatedAt)) }}</td>
          <td class="text-center">
            <button @click.prevent="openModal(event, 'EDIT_EVENT')" class="btn btn-sm btn-secondary me-2">Edit</button>
            <button @click.prevent="openModal(event, 'DELETE_EVENT')" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedEvents.length === 0">
          <td class="text-center" colspan="8">
            <span class="text-danger">Currently, there are no events listed.</span>
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

  <!-- Modal for adding / editing / deleting event. -->
  <EventModal
    v-if="isEventModalOpen"
    :event="selectedData"
    :modalAction="modalAction"
    @close="isEventModalOpen = false"
    @dispatchParent="loadPageContent"
  />
</template>

<script>
import { watch, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import { useHelper } from '@/store/helpers'
import { LIST_LIMIT, LIST_OFFSET } from '@/store/constants'
import EventModal from './EventModal.vue'

export default {
  components: {
    EventModal
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

    const events = ref([])
    const eventsCount = ref(0)

    const selectedData = ref(null)
    const isEventModalOpen = ref(false)

    const currentPage = ref(1) // Default value for current page number.
    const perPage = LIST_LIMIT // Number of items per page.
    const sortKey = ref('updatedAt') // Current column to sort by.
    const sortOrder = ref('desc') // Sort order: 'asc' or 'desc'.

    const modalAction = ref('') // Modal action.

    /**
     * Watchers: Watch for route query changes.
     */
    watch(() => route.params.currentPage, (newPage) => {
      fetchEvents(perPage, (newPage - 1) * perPage)
    })

    /**
     * Regular Methods.
     */
    const fetchEvents = async (limit, offset) => {
      limit = limit || LIST_LIMIT
      offset = offset || LIST_OFFSET

      // Request params.
      const requestParams = {
        limit,
        offset
      }
      await store.dispatch('fetchEvents', requestParams)
        .then(
          (response) => {
            events.value = response.data.events
            eventsCount.value = response.data.eventsCount
            // toast.info('Events list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          toast.error(handleApiError(error, 'Failed to fetch the list of events.'))
        })
    }

    const openModal = (data, action) => {
      const params = action.split('_')
      if (params[1] === 'EVENT') {
        modalAction.value = params[0]
        switch (modalAction.value) {
          case 'ADD':
            selectedData.value = null
            break

          case 'EDIT':
          case 'DELETE':
            // Clone the event or set to NULL for NEW.
            selectedData.value = { ...data }
            break
        }
        isEventModalOpen.value = true
      }
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
        router.push({ name: 'events', params: { currentPage: currentPage.value } })
      }
    }

    const loadPageContent = () => {
      // Fetch the paginated events list.
      fetchEvents(perPage, (currentPage.value - 1) * perPage)

      // Fetch and store all events in the Vuex store.
      fetchEventsFeed()

      // Fetch and store all organisers in the Vuex store.
      fetchOrganisersFeed()
    }

    /**
     * Computed methods.
     */
    const sortedEvents = computed(() => {
      const sorted = [...events.value].sort((a, b) => {
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

    const paginatedEvents = computed(() => {
      /*
      const start = (this.currentPage - 1) * this.perPage
      const end = start + this.perPage
      return this.sortedEvents.slice(start, end)
      */
      return sortedEvents.value
    })

    const totalPages = computed(() => {
      return Math.ceil(eventsCount.value / perPage)
    })

    /**
     * onMounted lifecycle hook: Executes when the component is mounted.
     */
    onMounted(() => {
      // Current page based on route parameter.
      currentPage.value = Number(route.params.currentPage) || 1

      // Load page content.
      loadPageContent()
    })

    return {
      toast,
      events,
      eventsCount,
      currentPage,
      perPage,
      sortKey,
      sortOrder,
      paginatedEvents,
      totalPages,
      sortBy,
      getSortClass,
      goToPage,
      selectedData,
      isEventModalOpen,
      helperIsOdd,
      helperFormatDateTime,
      handleApiError,
      fetchEvents,
      fetchEventsFeed,
      openModal,
      modalAction,
      loadPageContent
    }
  }
}
</script>

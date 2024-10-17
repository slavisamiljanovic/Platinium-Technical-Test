<template>
  <div class="list-wrapper">
    <h1 class="mt-2">Organisers List</h1>

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
          <button @click.prevent="openModal(null, 'ADD_ORGANISER')" class="btn btn-primary">Add Organiser</button>
        </li>
      </ul>
    </nav>

    <table class="table mt-3 table-list">
      <thead>
        <tr>
          <th class="id text-start"><a href="#" @click.prevent="sortBy('id')">ID <i :class="getSortClass('id')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('name')">Name <i :class="getSortClass('name')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('city')">City <i :class="getSortClass('city')"></i></a></th>
          <th class="text-start"><a href="#" @click.prevent="sortBy('phone')">Phone <i :class="getSortClass('phone')"></i></a></th>
          <th class="description text-start"><a href="#" @click.prevent="sortBy('description')">Description <i :class="getSortClass('description')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('createdAt')">Created At <i :class="getSortClass('createdAt')"></i></a></th>
          <th class="text-end"><a href="#" @click.prevent="sortBy('updatedAt')">Updated At <i :class="getSortClass('updatedAt')"></i></a></th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(organiser, index) in paginatedOrganisers"
          :key="organiser.id"
          :class="{'odd': helperIsOdd(index), 'even': !helperIsOdd(index)}"
        >
          <td class="id text-start">{{ organiser.id }}</td>
          <td class="text-start">{{ organiser.name }}</td>
          <td class="text-start">{{ organiser.city }}</td>
          <td class="text-start">{{ organiser.phone }}</td>
          <td class="description text-start">{{ organiser.description }}</td>
          <td class="text-end">{{ helperFormatDateTime(new Date(organiser.createdAt)) }}</td>
          <td class="text-end">{{ helperFormatDateTime(new Date(organiser.updatedAt)) }}</td>
          <td class="text-center">
            <button @click.prevent="openModal(organiser, 'EDIT_ORGANISER')" class="btn btn-sm btn-secondary me-2">Edit</button>
            <button @click.prevent="openModal(organiser, 'DELETE_ORGANISER')" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedOrganisers.length === 0">
          <td class="text-center" colspan="8">
            <span class="text-danger">Currently, there are no organisers listed.</span>
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

  <!-- Modal for adding / editing / deleting organiser. -->
  <OrganiserModal
    v-if="isOrganiserModalOpen"
    :organiser="selectedData"
    :modalAction="modalAction"
    @close="isOrganiserModalOpen = false"
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
import OrganiserModal from './OrganiserModal.vue'

export default {
  components: {
    OrganiserModal
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

    const organisers = ref([])
    const organisersCount = ref(0)

    const selectedData = ref(null)
    const isOrganiserModalOpen = ref(false)

    const currentPage = ref(1) // Default value for current page number.
    const perPage = LIST_LIMIT // Number of items per page.
    const sortKey = ref('updatedAt') // Current column to sort by.
    const sortOrder = ref('desc') // Sort order: 'asc' or 'desc'.

    const modalAction = ref('') // Modal action.

    /**
     * Watchers: Watch for route query changes.
     */
    watch(() => route.params.currentPage, (newPage) => {
      fetchOrganisers(perPage, (newPage - 1) * perPage)
    })

    /**
     * Regular Methods.
     */
    const fetchOrganisers = async (limit, offset) => {
      limit = limit || LIST_LIMIT
      offset = offset || LIST_OFFSET

      // Request params.
      const requestParams = {
        limit,
        offset
      }
      await store.dispatch('fetchOrganisers', requestParams)
        .then(
          (response) => {
            organisers.value = response.data.organisers
            organisersCount.value = response.data.organisersCount
            // toast.info('Organisers list fetched successfully.')
          }
        )
        .catch(error => {
          // Handle error response.
          toast.error(handleApiError(error, 'Failed to fetch the list of organisers.'))
        })
    }

    const openModal = (data, action) => {
      const params = action.split('_')
      if (params[1] === 'ORGANISER') {
        modalAction.value = params[0]
        switch (modalAction.value) {
          case 'ADD':
            selectedData.value = null
            break

          case 'EDIT':
          case 'DELETE':
            // Clone the organiser or set to NULL for NEW.
            selectedData.value = { ...data }
            break
        }
        isOrganiserModalOpen.value = true
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
        router.push({ name: 'organisers', params: { currentPage: currentPage.value } })
      }
    }

    const loadPageContent = () => {
      // Fetch the paginated organisers list.
      fetchOrganisers(perPage, (currentPage.value - 1) * perPage)

      // Fetch and store all events in the Vuex store.
      fetchEventsFeed()

      // Fetch and store all organisers in the Vuex store.
      fetchOrganisersFeed()
    }

    /**
     * Computed methods.
     */
    const sortedOrganisers = computed(() => {
      const sorted = [...organisers.value].sort((a, b) => {
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

    const paginatedOrganisers = computed(() => {
      /*
      const start = (this.currentPage - 1) * this.perPage
      const end = start + this.perPage
      return this.sortedOrganisers.slice(start, end)
      */
      return sortedOrganisers.value
    })

    const totalPages = computed(() => {
      return Math.ceil(organisersCount.value / perPage)
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
      organisers,
      organisersCount,
      currentPage,
      perPage,
      sortKey,
      sortOrder,
      paginatedOrganisers,
      totalPages,
      sortBy,
      getSortClass,
      goToPage,
      selectedData,
      isOrganiserModalOpen,
      helperIsOdd,
      helperFormatDateTime,
      handleApiError,
      fetchOrganisers,
      fetchOrganisersFeed,
      openModal,
      modalAction,
      loadPageContent
    }
  }
}
</script>

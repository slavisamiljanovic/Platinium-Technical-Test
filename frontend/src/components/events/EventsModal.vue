<template>
  <div class="modal fade show" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Events for: <b>{{ ticket.name }}</b></h5>
          <button type="button" class="btn-close" @click.prevent="closeModal"></button>
        </div>
        <div class="modal-body">
          <table class="table mt-3 table-list">
            <thead>
              <tr>
                <th class="text-start">ID</th>
                <th class="text-start">Name</th>
                <th class="text-start">Organiser</th>
                <th class="text-center">Active</th>
                <th class="text-end">Created At</th>
                <th class="text-end">Updated At</th>
              </tr>
              <tr
                v-for="(event, index) in ticket.events"
                :key="event.id"
                :class="{'odd': helperIsOdd(index), 'even': !helperIsOdd(index)}"
              >
                <td class="text-start">{{ event.id }}</td>
                <td class="text-start">{{ event.name }}</td>
                <td class="text-start">{{ event.organiser.name }}</td>
                <td class="text-center">
                  <span :class="{'text-success': event.isActive, 'text-danger': !event.isActive}">{{ event.isActive ? 'Yes' : 'No' }}</span>
                </td>
                <td class="text-end">{{ helperFormatDateTime(new Date(event.createdAt)) }}</td>
                <td class="text-end">{{ helperFormatDateTime(new Date(event.updatedAt)) }}</td>
              </tr>
            </thead>
            <tbody>
              <tr v-if="ticket.events.length === 0">
                <td class="text-center" colspan="6">
                  <span class="text-danger">Currently, there are no events listed.</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click.prevent="closeModal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-backdrop fade show"></div>
</template>

<script>
import { defineComponent } from 'vue'
import { useHelper } from '@/store/helpers'
import 'vue-select/dist/vue-select.css'

export default defineComponent({
  components: {
    // Components.
  },
  emits: ['close'],
  props: {
    ticket: Object
  },
  setup () {
    // Destructure methods from the helper.
    const {
      helperIsOdd,
      helperFormatDateTime
    } = useHelper()

    return {
      helperIsOdd,
      helperFormatDateTime
    }
  },
  data () {
    return {}
  },
  mounted () {
    // Add event listener on mount.
    document.addEventListener('keydown', this.handleKeydown)
    document.body.classList.add('disable-scroll')
  },
  methods: {
    closeModal () {
      this.$emit('close')
    },
    handleKeydown (event) {
      if (event.key === 'Escape') {
        this.closeModal()
      }
    }
  },
  beforeUnmount () {
    // Clean up the event listener.
    document.removeEventListener('keydown', this.handleKeydown)
    document.body.classList.remove('disable-scroll')
  }
})
</script>

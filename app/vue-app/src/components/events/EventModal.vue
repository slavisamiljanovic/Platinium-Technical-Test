<template>
  <div :class="{ 'd-block': !hideModal, 'd-none': hideModal }">
    <div class="modal fade show" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ (modalAction === 'ADD') ? 'Add' : '' }}
              {{ ((modalAction === 'EDIT') ? 'Edit' : '') }}
              {{ (modalAction === 'DELETE') ? 'Delete' : '' }}
              Event
            </h5>
            <button type="button" class="btn-close" @click.prevent="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="save" class="form-group mt-3">
              <div class="mb-3">
                <label for="name" class="d-flex form-label required text-start small-text mb-1">Event Name</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Event Name"
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
                <label for="organiser" class="d-flex form-label required text-start small-text mb-1">Organiser</label>
                <v-select
                  v-model="formData.organiser"
                  placeholder="Select organiser"
                  label="name"
                  :options="organisers"
                  :close-on-select="true"
                  :clear-on-select="false"
                  :searchable="true"
                  :disabled="(modalAction === 'DELETE')"
                ></v-select>
              </div>
              <div class="form-check form-check-inline mb-3">
                <input
                  v-model="formData.isActive"
                  class="form-check-input larger-checkbox"
                  type="checkbox"
                  id="isActive"
                  :disabled="(modalAction === 'DELETE')"
                />
                <label for="isActive" class="form-check-label">Event Active</label>
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
              <span class="error-placeholder response-error mt-0 mb-4">
                <span v-if="responseError" class="error text-danger">{{ responseError }}</span>
              </span>
            </form>
          </div>
          <div class="modal-footer">
            <p v-if="(modalAction === 'DELETE')" class="text-danger">Are you sure you want to delete this event?</p>
            <button type="button" class="btn btn-secondary" @click.prevent="closeModal">Close</button>
            <button
              type="button"
              class="btn"
              :class="{ 'btn-primary': (modalAction !== 'DELETE'), 'btn-danger': (modalAction === 'DELETE') }"
              @click.prevent="submitAction"
            >
              {{ (modalAction === 'DELETE') ? 'Delete' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  </div>
</template>

<script>
import { ref, getCurrentInstance, onMounted, defineComponent, computed } from 'vue'
import { useStore } from 'vuex'
import { useToast } from 'vue-toastification'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { useHelper } from '@/store/helpers'

export default defineComponent({
  components: {
    vSelect
  },
  props: {
    event: Object,
    modalAction: String
  },
  setup (props, { emit }) {
    // Access Vuex store.
    const store = useStore()

    // Get toast interface.
    const toast = useToast()

    // Destructure methods from the helper.
    const { handleApiError, isObjectEmpty } = useHelper()

    const hideModal = ref(false)
    const formData = ref({
      name: props.event?.name || '',
      organiser: props.event?.organiser || '',
      description: props.event?.description || '',
      isActive: props.event?.isActive || ''
    })
    const nameError = ref('')
    const cityError = ref('')
    const responseError = ref(null) // Response error.
    const organisers = ref([])
    const organisersCount = ref(0)

    if (!isObjectEmpty(store.state.organisersFeed)) {
      organisers.value = store.state.organisersFeed.organisers
      organisersCount.value = store.state.organisersFeed.organisersCount
    }

    // Create a ref for the input element.
    const nameInput = ref(null)
    const instance = getCurrentInstance()
    const focusNameInput = () => {
      setTimeout(
        () => {
          instance.refs.nameInput.focus()
        }, 250
      )
    }

    /**
     * Regular Methods.
     */
    const handleKeydown = (event) => {
      if (event.key === 'Escape') {
        closeModal()
      }
    }

    const closeModal = () => {
      emit('close')
    }

    const validateName = () => {
      nameError.value = formData.value.name !== ''
        ? ''
        : 'Event name is required field.'
    }

    const submitAction = () => {
      // Optionally, perform additional validation before submitting.
      validateName()
      if (!isFormValid.value) {
        return
      }

      const eventId = props.event?.id
      const event = { ...formData.value }
      event.isActive = !!event.isActive
      dispatchAction(event, eventId)
    }

    const dispatchAction = async (event, eventId) => {
      hideModal.value = true
      if (props.modalAction === 'ADD') {
        // Creating a new data.
        // Request params.
        const requestParams = {
          event
        }
        await store.dispatch('saveEvent', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The event has been created successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to save the event.')
            toast.error(responseError.value)
          })
      } else if (props.modalAction === 'EDIT') {
        // Updating an existing data.
        // Request params.
        const requestParams = {
          event,
          eventId
        }
        await store.dispatch('updateEvent', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The event has been saved successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to save the event.')
            toast.error(responseError.value)
          })
      } else if (props.modalAction === 'DELETE') {
        // Updating an existing data.
        // Request params.
        const requestParams = {
          event,
          eventId
        }
        await store.dispatch('deleteEvent', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The event has been deleted successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to delete the event.')
            toast.error(responseError.value)
          })
      }
    }

    /**
     * Computed methods.
     */
    const isFormValid = computed(() => {
      return !nameError.value
    })

    /**
     * onMounted lifecycle hook: Executes when the component is mounted.
     */
    onMounted(() => {
      // Add event listener on mount.
      document.addEventListener('keydown', handleKeydown)
      document.body.classList.add('disable-scroll')
      responseError.value = null
      hideModal.value = false
      focusNameInput()
    })

    return {
      hideModal,
      formData,
      nameError,
      cityError,
      nameInput,
      responseError,
      organisers,
      organisersCount,
      closeModal,
      focusNameInput,
      handleKeydown,
      validateName,
      handleApiError,
      submitAction,
      dispatchAction
    }
  },
  beforeUnmount () {
    // Clean up the event listener.
    document.removeEventListener('keydown', this.handleKeydown)
    document.body.classList.remove('disable-scroll')
  }
})
</script>

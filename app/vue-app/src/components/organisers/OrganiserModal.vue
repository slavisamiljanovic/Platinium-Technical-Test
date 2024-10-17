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
              Organiser
            </h5>
            <button type="button" class="btn-close" @click.prevent="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="save" class="form-group mt-3">
              <div class="mb-3">
                <label for="name" class="d-flex form-label required text-start small-text mb-1">Organiser Name</label>
                <input
                  v-model="formData.name"
                  type="text"
                  class="form-control"
                  id="name"
                  placeholder="Organiser Name"
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
                <label for="city" class="d-flex form-label required text-start small-text mb-1">Organiser City</label>
                <input
                  v-model="formData.city"
                  type="text"
                  class="form-control"
                  id="city"
                  placeholder="Organiser City"
                  required
                  @blur="validateCity"
                  :disabled="(modalAction === 'DELETE')"
                />
                <span class="error-placeholder">
                  <span v-if="cityError" class="error text-danger">{{ cityError }}</span>
                </span>
              </div>
              <div class="mb-3">
                <label for="phone" class="d-flex form-label text-start small-text mb-1">Organiser Phone</label>
                <input
                  v-model="formData.phone"
                  type="text"
                  class="form-control"
                  id="phone"
                  placeholder="Organiser Phone"
                  :disabled="(modalAction === 'DELETE')"
                />
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
            <p v-if="(modalAction === 'DELETE')" class="text-danger">Are you sure you want to delete this organiser?</p>
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
import { useHelper } from '@/store/helpers'

export default defineComponent({
  props: {
    organiser: Object,
    modalAction: String
  },
  setup (props, { emit }) {
    // Access Vuex store.
    const store = useStore()

    // Get toast interface.
    const toast = useToast()

    // Destructure methods from the helper.
    const { handleApiError } = useHelper()

    const hideModal = ref(false)
    const formData = ref({
      name: props.organiser?.name || '',
      city: props.organiser?.city || '',
      phone: props.organiser?.phone || '',
      description: props.organiser?.description || ''
    })
    const nameError = ref('')
    const cityError = ref('')
    const responseError = ref(null) // Response error.

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
        : 'Organiser name is required field.'
    }

    const validateCity = () => {
      cityError.value = formData.value.city !== ''
        ? ''
        : 'Organiser city is required field.'
    }

    const submitAction = () => {
      // Optionally, perform additional validation before submitting.
      validateName()
      validateCity()
      if (!isFormValid.value) {
        return
      }

      const organiserId = props.organiser?.id
      const organiser = { ...formData.value }
      dispatchAction(organiser, organiserId)
    }

    const dispatchAction = async (organiser, organiserId) => {
      hideModal.value = true
      if (props.modalAction === 'ADD') {
        // Creating a new data.
        // Request params.
        const requestParams = {
          organiser
        }
        await store.dispatch('saveOrganiser', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The organiser has been created successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to save the organiser.')
            toast.error(responseError.value)
          })
      } else if (props.modalAction === 'EDIT') {
        // Updating an existing data.
        // Request params.
        const requestParams = {
          organiser,
          organiserId
        }
        await store.dispatch('updateOrganiser', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The organiser has been saved successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to save the organiser.')
            toast.error(responseError.value)
          })
      } else if (props.modalAction === 'DELETE') {
        // Updating an existing data.
        // Request params.
        const requestParams = {
          organiser,
          organiserId
        }
        await store.dispatch('deleteOrganiser', requestParams)
          .then(
            () => {
              hideModal.value = false
              toast.success('The organiser has been deleted successfully.')
              closeModal()
              emit('dispatchParent')
            }
          )
          .catch(error => {
            // Handle error response.
            hideModal.value = false
            responseError.value = handleApiError(error, 'Failed to delete the organiser.')
            toast.error(responseError.value)
          })
      }
    }

    /**
     * Computed methods.
     */
    const isFormValid = computed(() => {
      return !nameError.value && !cityError.value
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
      closeModal,
      focusNameInput,
      handleKeydown,
      validateName,
      validateCity,
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

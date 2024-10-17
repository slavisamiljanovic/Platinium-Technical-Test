import { createStore } from 'vuex'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import {
  API_DOMAIN,
  LIST_LIMIT,
  LIST_OFFSET
} from './constants'
import { useHelper, TokenExpiration } from './helpers'
import { LoggerService } from '@/plugins/services/logger'

// Logger service.
const loggerService = new LoggerService()

// Destructure methods from the helper.
const {
  loaderStartLoading,
  loaderStopLoading,
  isLoggedIn,
  isTokenAboutToExpire,
  handleApiError
} = useHelper()

// Get toast interface.
const toast = useToast()

export default createStore({
  state: {
    status: '',
    apiURL: API_DOMAIN + '/api/',
    token: localStorage.getItem('token') || '',
    user: {},
    refreshTimeoutId: null,
    eventsFeed: {},
    organisersFeed: {}
  },
  getters: {
    isUserLoggedIn (state): boolean {
      const token = state.token || ''
      const result = isLoggedIn(token)
      return result
    },
    authStatus: state => state.status
  },
  mutations: {
    AUTH_REQUEST (state) {
      state.status = 'loading'
    },
    AUTH_SUCCESS (state, data) {
      state.status = 'success'
      state.token = data.token
      state.user = data.user
      localStorage.setItem('token', data.token)
    },
    AUTH_ERROR (state) {
      state.status = 'error'
      localStorage.removeItem('token')
    },
    USER_LOGOUT (state) {
      state.status = ''
      state.token = ''
      state.user = {}
      localStorage.removeItem('token')
    },
    SET_REFRESH_TIMEOUT_ID (state, timeoutId) {
      state.refreshTimeoutId = timeoutId
    },
    CLEAR_REFRESH_TIMEOUT (state) {
      if (state.refreshTimeoutId) {
        clearTimeout(state.refreshTimeoutId)
        state.refreshTimeoutId = null
      }
    },
    FETCH_EVENTS_FEED (state, data) {
      state.eventsFeed = data
    },
    FETCH_ORGANISERS_FEED (state, data) {
      state.organisersFeed = data
    }
  },
  actions: {
    updateToken ({ commit }, token: string) {
      commit('setToken', token)
    },
    login ({ commit }, loginRequest) {
      // Start loading.
      loaderStartLoading()

      return new Promise((resolve, reject) => {
        commit('AUTH_REQUEST')
        axios(
          {
            url: this.state.apiURL + 'users/login',
            method: 'POST',
            data: loginRequest
          }
        )
          .then(
            response => {
              // The API responds with a user data upon successful login.
              loggerService.log('$store.dispatch(login) -> response', { logType: 'info', logData: response })
              const { user } = response.data
              const token = user.token

              // Store the token in Vuex and localStorage.
              // Update Vuex state with user data and user token.
              this.commit('AUTH_SUCCESS', { token, user })

              // Start token fefresh timer.
              this.dispatch('startTokenRefreshTimer')

              // End loading.
              loaderStopLoading()

              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            commit('AUTH_ERROR')
            reject(error)
          })
      })
    },
    logout ({ commit }) {
      // Clear user login data.
      commit('USER_LOGOUT')

      // Clear any existing timer(s).
      commit('CLEAR_REFRESH_TIMEOUT')
    },
    tokenExpired ({ commit }) {
      commit('USER_LOGOUT')
      toast.warning('Your token has expired. Please log in again.')
    },
    startTokenRefreshTimer ({ state, commit, dispatch }) {
      // Clear any existing timer(s).
      commit('CLEAR_REFRESH_TIMEOUT')
      if (!state.token) {
        return
      }
      const tokenExpiration: TokenExpiration = isTokenAboutToExpire(state.token)
      if (!tokenExpiration.isAboutToExpire && tokenExpiration.refreshTime > 0) {
        const timeoutId = setTimeout(
          () => {
            dispatch('refreshToken').then().catch(
              error => {
                handleApiError(error, 'Failed to refresh the token.')
              }
            )
          },
          tokenExpiration.refreshTime * 1000
        )
        // Store the timeout ID in the state.
        commit('SET_REFRESH_TIMEOUT_ID', timeoutId)
      }
    },
    refreshToken () {
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + 'user',
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // The API responds with a user data upon successful login.
              loggerService.log('$store.dispatch(refreshToken) -> response', { logType: 'debug', logData: response })
              const { user } = response.data
              const token = user.token

              // Store the token in Vuex and localStorage.
              // Update Vuex state with user data and user token.
              this.commit('AUTH_SUCCESS', { token, user })

              // Start token fefresh timer.
              this.dispatch('startTokenRefreshTimer')

              resolve(response)
            }
          )
          .catch(error => {
            reject(error)
          })
      })
    },
    fetchTickets (_, requestParams) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + 'tickets',
            method: 'GET',
            params: {
              limit: requestParams.limit || LIST_LIMIT,
              offset: requestParams.offset || LIST_OFFSET
            },
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchTickets) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    saveTicket (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        delete data.ticketId
        axios(
          {
            url: this.state.apiURL + 'tickets',
            method: 'POST',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(saveTicket) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    fetchTicket (_, ticketId) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + `tickets/${ticketId}`,
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchTicket) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    updateTicket (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const ticketId = data.ticketId
        delete data.ticketId
        axios(
          {
            url: this.state.apiURL + `tickets/${ticketId}`,
            method: 'PUT',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(updateTicket) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    deleteTicket (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const ticketId = data.ticketId
        delete data.ticketId
        axios(
          {
            url: this.state.apiURL + `tickets/${ticketId}`,
            method: 'DELETE',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(deleteTicket) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    fetchEvents (_, requestParams) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + 'events',
            method: 'GET',
            params: {
              limit: requestParams.limit || LIST_LIMIT,
              offset: requestParams.offset || LIST_OFFSET
            },
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchEvents) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    saveEvent (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        delete data.eventId
        axios(
          {
            url: this.state.apiURL + 'events',
            method: 'POST',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(saveEvent) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    fetchEventsFeed ({ commit }) {
      return new Promise((resolve, reject) => {
        commit('FETCH_EVENTS_FEED', {})
        axios(
          {
            url: this.state.apiURL + 'events/feed',
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // Store the data in Vuex.
              loggerService.log('$store.dispatch(fetchEventsFeed) -> response', { logType: 'info', logData: response })
              const { data } = response
              this.commit('FETCH_EVENTS_FEED', data)

              resolve(response)
            }
          )
          .catch(error => {
            reject(error)
          })
      })
    },
    fetchEvent (_, eventId) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + `events/${eventId}`,
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchEvent) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    updateEvent (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const eventId = data.eventId
        delete data.eventId
        axios(
          {
            url: this.state.apiURL + `events/${eventId}`,
            method: 'PUT',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(updateEvent) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    deleteEvent (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const eventId = data.eventId
        delete data.eventId
        axios(
          {
            url: this.state.apiURL + `events/${eventId}`,
            method: 'DELETE',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(deleteEvent) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    fetchOrganisers (_, requestParams) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + 'organisers',
            method: 'GET',
            params: {
              limit: requestParams.limit || LIST_LIMIT,
              offset: requestParams.offset || LIST_OFFSET
            },
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchOrganisers) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    saveOrganiser (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        delete data.organiserId
        axios(
          {
            url: this.state.apiURL + 'organisers',
            method: 'POST',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(saveOrganiser) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    fetchOrganisersFeed ({ commit }) {
      return new Promise((resolve, reject) => {
        commit('FETCH_ORGANISERS_FEED', {})
        axios(
          {
            url: this.state.apiURL + 'organisers/feed',
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // Store the data in Vuex.
              loggerService.log('$store.dispatch(fetchOrganisersFeed) -> response', { logType: 'info', logData: response })
              const { data } = response
              this.commit('FETCH_ORGANISERS_FEED', data)

              resolve(response)
            }
          )
          .catch(error => {
            reject(error)
          })
      })
    },
    fetchOrganiser (_, organiserId) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        axios(
          {
            url: this.state.apiURL + `organisers/${organiserId}`,
            method: 'GET',
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(fetchOrganiser) -> response', { logType: 'info', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    updateOrganiser (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const organiserId = data.organiserId
        delete data.organiserId
        axios(
          {
            url: this.state.apiURL + `organisers/${organiserId}`,
            method: 'PUT',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(updateOrganiser) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    },
    deleteOrganiser (_, data) {
      // Start loading.
      loaderStartLoading()
      return new Promise((resolve, reject) => {
        const organiserId = data.organiserId
        delete data.organiserId
        axios(
          {
            url: this.state.apiURL + `organisers/${organiserId}`,
            method: 'DELETE',
            data: data,
            headers: { Authorization: 'Token ' + this.state.token }
          }
        )
          .then(
            response => {
              // End loading.
              loggerService.log('$store.dispatch(deleteOrganiser) -> response', { logType: 'debug', logData: response })
              loaderStopLoading()
              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            reject(error)
          })
      })
    }
  },
  modules: {
    // You can add modules here.
  }
})

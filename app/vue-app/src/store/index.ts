import { createStore } from 'vuex'
import axios from 'axios'
import { useToast } from 'vue-toastification'
import {
  API_DOMAIN,
  LIST_LIMIT,
  LIST_OFFSET
} from './constants'
import { useHelper } from './helpers'
import { LoggerService } from '@/plugins/services/logger'

// Logger service.
const loggerService = new LoggerService()

// Destructure methods from the helper.
const {
  loaderStartLoading,
  loaderStopLoading,
  isLoggedIn
} = useHelper()

// Get toast interface.
const toast = useToast()

export default createStore({
  state: {
    status: '',
    apiURL: API_DOMAIN + '/api/',
    token: localStorage.getItem('token') || '',
    user: {},
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
      commit('USER_LOGOUT')
    },
    tokenExpired ({ commit }) {
      commit('USER_LOGOUT')
      toast.warning('Your token has expired. Please log in again.')
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
              // Store the data in Vuex.
              loggerService.log('$store.dispatch(fetchTickets) -> response', { logType: 'info', logData: response })

              // End loading.
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
              loggerService.log('$store.dispatch(fetchTicket) -> response', { logType: 'info', logData: response })

              // End loading.
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
              loggerService.log('$store.dispatch(fetchEvents) -> response', { logType: 'info', logData: response })

              // End loading.
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
              loggerService.log('$store.dispatch(fetchOrganisers) -> response', { logType: 'info', logData: response })

              // End loading.
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
    }
  },
  modules: {
    // You can add modules here.
  }
})

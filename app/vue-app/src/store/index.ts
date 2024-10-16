import { createStore } from 'vuex'
import axios from 'axios'
import {
  API_DOMAIN,
  LIST_LIMIT,
  LIST_OFFSET
} from './constants'
import { useHelper } from './helpers'

// Destructure methods from the helper.
const {
  loaderStartLoading,
  loaderStopLoading
} = useHelper()

export default createStore({
  state: {
    status: '',
    apiURL: API_DOMAIN + '/api/',
    token: localStorage.getItem('token') || '',
    user: {},
    tickets: {},
    eventsFeed: {},
    organisersFeed: {}
  },
  getters: {
    isLoggedIn: state => !!state.token,
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
    },
    AUTH_ERROR (state) {
      state.status = 'error'
    },
    USER_LOGOUT (state) {
      state.status = ''
      state.token = ''
      state.user = {}
    },
    FETCH_TICKETS (state, data) {
      state.tickets = data
    },
    FETCH_EVENTS_FEED (state, data) {
      state.eventsFeed = data
    },
    FETCH_ORGANISERS_FEED (state, data) {
      state.organisersFeed = data
    }
  },
  actions: {
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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(login) -> response: ', response)
              }

              // The API responds with a user data upon successful login.
              const { user } = response.data
              const token = user.token

              // Store the token in Vuex and localStorage.
              // Update Vuex state with user data and user token.
              this.commit('AUTH_SUCCESS', { token, user })
              localStorage.setItem('token', token)

              // End loading.
              loaderStopLoading()

              resolve(response)
            }
          )
          .catch(error => {
            // End loading.
            loaderStopLoading()
            commit('AUTH_ERROR')
            localStorage.removeItem('token')
            reject(error)
          })
      })
    },
    logout ({ commit }) {
      commit('USER_LOGOUT')
      localStorage.removeItem('token')
    },
    fetchTickets ({ commit }, requestParams) {
      // Start loading.
      loaderStartLoading()

      return new Promise((resolve, reject) => {
        commit('FETCH_TICKETS', {})
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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchTickets) -> response: ', response)
              }

              // Store the data in Vuex.
              const { data } = response
              this.commit('FETCH_TICKETS', data)

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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchTicket) -> response: ', response)
              }

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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchEvents) -> response: ', response)
              }

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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchEventsFeed) -> response: ', response)
              }

              // Store the data in Vuex.
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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchOrganisers) -> response: ', response)
              }

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
              if (process.env.NODE_ENV === 'development') {
                console.info('DEBUG-INFO: $store.dispatch(fetchOrganisersFeed) -> response: ', response)
              }

              // Store the data in Vuex.
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

import { shallowMount } from '@vue/test-utils'
import AdminLogin from '@/components/AdminLogin.vue'
import store from '@/store'
import { createRouter, createWebHistory } from 'vue-router'

// Create a mock router for testing.
const routes = [
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: { template: '<div>Dashboard</div>' } // Mock component for testing.
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Mocking the router's push method.
const mockPush = jest.fn()

router.push = mockPush

describe('AdminLogin.vue', () => {
  beforeEach(() => {
    // Clear all mocks before each test.
    jest.clearAllMocks()
  })

  it('should log in with valid credentials', async () => {
    // Mock successful login response.
    const userData = { token: 'mockToken', user: { name: 'Admin', email: 'admin@example.com' } }
    store.dispatch = jest.fn(() => Promise.resolve({ data: { user: userData } }))

    const wrapper = shallowMount(AdminLogin, {
      global: {
        plugins: [store, router]
      }
    })

    // Set valid credentials.
    wrapper.setData({
      email: 'admin@example.com',
      password: 'validpassword'
    })

    // Trigger form submit.
    await wrapper.vm.handleSubmit()

    // Check if redirected to dashboard.
    expect(mockPush).toHaveBeenCalledWith('/dashboard')
  })

  it('should show error message on failed login', async () => {
    // Mock a failed login attempt with a 401 response.
    // eslint-disable-next-line
    store.dispatch = jest.fn(() => Promise.reject({
      response: {
        status: 401,
        data: { message: 'Invalid credentials' }
      }
    }))

    const wrapper = shallowMount(AdminLogin, {
      global: {
        plugins: [store, router]
      }
    })

    // Set invalid credentials.
    wrapper.setData({
      email: 'test@example.com',
      password: 'wrongpassword'
    })

    // Trigger form submit.
    await wrapper.vm.handleSubmit()

    // Wait for the error to be set.
    await wrapper.vm.$nextTick()

    // Check if responseError is set after failure.
    expect(wrapper.vm.responseError).toBe('Failed to login. Please try again.')
  })
})

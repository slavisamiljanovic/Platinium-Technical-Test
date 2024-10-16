<template>
  <div class="container login-form">
    <h1 class="mt-2">Admin Login</h1>
    <h5>{{ msg }}</h5>
    <form @submit.prevent="handleSubmit" class="form-group mt-3">
      <div class="mb-3">
        <label for="email" class="d-flex form-label required text-start small-text mb-1">E-mail</label>
        <input
          v-model="email"
          type="email"
          class="form-control"
          id="email"
          placeholder="E-mail"
          required
          @blur="validateEmail"
          ref="emailInput"
        />
        <span class="error-placeholder">
          <span v-if="emailError" class="error">{{ emailError }}</span>
        </span>
      </div>
      <div class="mb-3">
        <label for="password" class="d-flex form-label required text-start small-text mb-1">Password</label>
        <input
          v-model="password"
          type="password"
          class="form-control"
          id="password"
          placeholder="Password"
          required
          @blur="validatePassword"
        />
        <span class="error-placeholder">
          <span v-if="passwordError" class="error">{{ passwordError }}</span>
        </span>
      </div>
      <span class="error-placeholder response-error mt-0 mb-4">
        <span v-if="responseError" class="error text-danger">{{ responseError }}</span>
      </span>
      <button type="submit" class="btn btn-primary login-button">Login</button>
    </form>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { useHelper } from '@/store/helpers'
import { LoginRequestModel } from '@/models/user'

export default defineComponent({
  name: 'AdminLogin',
  props: {
    msg: String
  },
  setup () {
    // Destructure methods from the helper.
    const { handleApiError } = useHelper()

    // Make it available inside methods.
    return {
      handleApiError
    }
  },
  mounted () {
    // Focus the email input field when the component is mounted.
    this.$refs.emailInput.focus()
  },
  computed: {
    isFormValid () {
      return !this.emailError && !this.passwordError && this.email && this.password
    }
  },
  data () {
    return {
      email: '',
      password: '',
      emailError: '',
      passwordError: '',
      responseError: null
    }
  },
  methods: {
    validateEmail () {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      this.emailError = emailPattern.test(this.email)
        ? ''
        : 'Invalid e-mail address.'
    },
    validatePassword () {
      this.passwordError = this.password.length >= 4
        ? ''
        : 'Password must be at least 4 characters.'
    },
    handleSubmit () {
      // Optionally, perform additional validation before submitting.
      this.validateEmail()
      this.validatePassword()
      if (!this.isFormValid) {
        return
      }

      // User login data.
      const userData = new LoginRequestModel(
        {
          email: this.email,
          password: this.password
        }
      )
      const loginRequest = {
        user: userData
      }

      // Submit the form (call an API to log in).
      this.$logger.log('AdminLogin -> handleSubmit() -> Logging in with', { logData: loginRequest })
      this.$store.dispatch('login', loginRequest)
        .then(
          () => {
            // Redirect to dashboard after successful login.
            this.$router.push('/dashboard')
          }
        )
        .catch(error => {
          // Handle error response.
          this.$logger.log('AdminLogin -> handleSubmit() -> error', { logType: 'error', logData: error })
          this.responseError = this.handleApiError(error, 'Failed to login. Please try again.')
        })
    }
  }
})
</script>

<style lang="scss">
.container {
  &.login-form {
    max-width: 32rem;
  }
  .login-button {
    width: 7.5rem;
  }
}
</style>

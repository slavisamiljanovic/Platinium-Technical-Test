<template>
  <header class="header d-flex justify-content-between align-items-center">
    <div class="logo-title">
      <router-link to="/dashboard">
        <img src="../assets/logo.png" alt="Technical Test - Tickets System" class="logo">
      </router-link>
      <h1 class="h5"><router-link to="/dashboard">Technical Test - Tickets System</router-link></h1>
    </div>
    <nav class="d-flex">
      <router-link class="nav-link" to="/">Home</router-link>
      <router-link v-if="isUserLoggedIn" class="nav-link" to="/dashboard">Dashboard</router-link>
      <router-link v-if="isUserLoggedIn" :class="{'nav-link': true, 'router-link-exact-active': isActiveRoute}" to="/tickets">Tickets</router-link>
      <router-link v-if="isUserLoggedIn" class="nav-link" to="/events">Events</router-link>
      <router-link v-if="isUserLoggedIn" class="nav-link" to="/organisers">Organisers</router-link>
      <router-link class="nav-link" to="/about">About</router-link>
      <router-link v-if="!isUserLoggedIn" class="nav-link" to="/login">Login</router-link>
      <a v-if="isUserLoggedIn" href="#" @click.prevent="logout()" class="nav-link">Logout</a>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'AppHeader',
  computed: {
    isUserLoggedIn () {
      return this.$store.getters.isUserLoggedIn
    },
    isActiveRoute () {
      return this.$route.path.startsWith('/tickets')
    }
  },
  methods: {
    logout () {
      this.$store.dispatch('logout')
        .then(
          () => {
            this.$router.push('/')
          }
        )
    }
  }
}
</script>

<style lang="scss">
.header {
  background-color: #f8f9fa;
  padding: 10px 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
.logo-title {
  display: flex;
  align-items: center;

  h1 {
    a {
      color: #3b3b3b;
      text-decoration: none;
      &:hover {
        text-decoration: none;
      }
    }
  }
}
.logo {
  width: 50px;
  height: 50px;
  margin-right: 10px;
}
</style>

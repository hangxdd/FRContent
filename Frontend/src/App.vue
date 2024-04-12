<template>
  <Nav v-if="useAuth.user" />
  <router-view />
</template>

<script setup>
import Nav from './components/Nav.vue'
import { authStore } from './stores/authstore'

const useAuth = authStore();
</script>
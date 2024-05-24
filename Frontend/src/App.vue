<template>
  <Nav v-if="useAuth.user" />
  <div class="flex justify-center items-center">
    <router-view />
  </div>
</template>

<script setup>
import Nav from "./components/Nav.vue";
import { authStore } from "./stores/authstore";

const useAuth = authStore();
</script>

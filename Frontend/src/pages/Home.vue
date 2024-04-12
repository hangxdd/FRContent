<template>
    <div class="container">
        <h1>Home page</h1>
        <div v-if="useAuth.user">
            <h3>{{ useAuth.user.name }}</h3>
            <h3>{{ useAuth.user.email }}</h3>
        </div>

        <div v-else>
            <h3>No data</h3>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { authStore } from '../stores/authstore'

import axios from 'axios';

const useAuth = authStore();

onMounted(async () => {
    useAuth.getUser();
})

</script>
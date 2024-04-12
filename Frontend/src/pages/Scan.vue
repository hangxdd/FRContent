<template>
    <h1>test</h1>
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
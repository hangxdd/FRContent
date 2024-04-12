<template>
    <div class="mt-10 py-6 flex flex-col justify-center sm:py-12">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div
                class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-light-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl">
            </div>
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div v-if="useAuth.user">
                    <h2 class="text-2xl font-bold text-center">Welcome, {{ useAuth.user.name }}!</h2>
                    <p class="text-center mt-4">Discover movies tailored to your emotions.</p>
                    <router-link to="/scan"
                        class="mt-8 p-2 w-full block text-white font-bold text-center bg-blue-500 rounded hover:bg-blue-600">
                        Start Exploring
                    </router-link>
                </div>

                <div v-else class="flex justify-center">
                    <h3 class="text-3xl font-bold">No data</h3>
                </div>

                <div class="mt-10">
                    <h2 class="text-2xl font-bold text-center">How It Works</h2>
                    <p class="text-center mt-4">Our facial recognition technology analyzes your emotions and recommends
                        movies that match.</p>
                </div>
            </div>
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
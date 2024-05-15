<template>
  <div v-if="!useAuth.user" class="flex justify-center items-center h-screen">
    <h3 class="text-3xl text-red-500 font-bold">No data</h3>
  </div>

  <div v-if="useAuth.user" class="mt-10 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
      <div
        class="absolute inset-0 bg-gradient-to-r from-blue-400 to-light-blue-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"
      ></div>
      <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
        <div>
          <div class="flex justify-center items-center -mt-10">
            <img
              src="/FRContent.svg"
              alt="FRContent Logo"
              class="h-8 w-auto sm:h-10 mb-4"
            />
          </div>
          <h2 class="text-2xl font-bold text-center">
            Welcome, {{ useAuth.user.name }}!
          </h2>
          <p class="text-center mt-4">Discover movies tailored to your emotions.</p>
          <router-link
            to="/detect"
            class="flex justify-center mt-8 p-2 w-full text-white font-semibold text-center bg-blue-500 rounded hover:bg-blue-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6 mr-1"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.834.166-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243-1.59-1.59"
              />
            </svg>
            Start Exploring
          </router-link>
        </div>

        <div v-if="useAuth.user" class="mt-10">
          <h2 class="text-2xl font-bold text-center">How It Works</h2>
          <p class="text-center mt-4">
            Our facial recognition technology analyzes your emotions and recommends movies
            that match.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { authStore } from "../stores/authstore";

const useAuth = authStore();

onMounted(async () => {
  useAuth.getUser();
});
</script>

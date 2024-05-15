<template>
  <div v-if="!useAuth.user" class="flex justify-center items-center h-screen">
    <h3 class="text-3xl text-red-500 font-bold">No data</h3>
  </div>
  <div v-if="useAuth.user" class="bg-white shadow-lg rounded-lg md:w-1/3 p-6 mt-10">
    <div class="flex flex-col">
      <div class="mb-4">
        <p class="flex mb-2 font-semibold">
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
              d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
            />
          </svg>

          Name
        </p>
        <h2
          class="text-md text-gray-500 bg-gray-100 px-3 py-1 border-2 border-gray-400 rounded"
        >
          {{ useAuth.user.name }}
        </h2>
      </div>
      <div class="mb-2">
        <p class="flex mb-2 font-semibold">
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
              d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25"
            />
          </svg>
          Email
        </p>
        <h2
          class="text-md text-gray-500 bg-gray-100 px-3 py-1 border-2 border-gray-400 rounded"
        >
          {{ useAuth.user.email }}
        </h2>
      </div>
      <router-link
        @click="logOutAndRedirect"
        to="/forgotpass"
        class="flex bg-blue-600 hover:bg-blue-500 text-white font-semibold justify-center text-center py-2 px-4 rounded mt-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-6 h-6 ml-1 mr-1"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"
          />
        </svg>
        Change password
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { authStore } from "../stores/authstore";
import { useToast } from "vue-toastification";

const useAuth = authStore();
const toast = useToast();

onMounted(async () => {
  await useAuth.getUser();
});

const logOutAndRedirect = () => {
  toast("You have been logged out");
  useAuth.logOut();
};
</script>

<template>
  <div class="flex min-h-screen flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="bg-gray-100 md:w-1/3 mx-auto p-4 rounded-xl shadow-md">
      <div class="text-center mb-8">
        <img src="/FRContent.svg" alt="FRContent Logo" class="mx-auto h-16 w-auto" />
      </div>
      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2
          class="mt-10 text-center text-md md:text-2xl font-bold leading-9 tracking-tight text-gray-900"
        >
          Sign in to your account
        </h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" @submit.prevent="submitLogin">
          <div>
            <label for="email" class="flex text-sm font-medium leading-6 text-gray-900"
              ><svg
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
              Email address</label
            >
            <div class="mt-2">
              <input
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                v-model="form.email"
              />
            </div>
          </div>

          <div>
            <div class="flex flex-col md:flex-row items-center justify-between">
              <label
                for="password"
                class="flex text-sm font-medium leading-6 text-gray-900"
                ><svg
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
                    d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z"
                  />
                </svg>
                Password</label
              >
              <div class="text-sm">
                <router-link
                  to="/forgotpass"
                  class="font-semibold text-blue-600 hover:text-blue-500"
                  >Forgot password?</router-link
                >
              </div>
            </div>
            <div class="mt-2">
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                v-model="form.password"
              />
            </div>
          </div>

          <div>
            <button
              :disabled="isButtonDisabled"
              type="submit"
              class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:bg-gray-400 disabled:text-gray-200 duration-200"
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
                  d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25"
                />
              </svg>
              Sign in
            </button>
          </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
          Not a member?
          {{ " " }}
          <router-link
            to="register"
            class="font-semibold leading-6 text-blue-600 hover:text-blue-500"
            >Register</router-link
          >
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { authStore } from "../stores/authstore";
import { useToast } from "vue-toastification";
import axios from "axios";

const router = useRouter();
const useAuth = authStore();
const toast = useToast();
const isButtonDisabled = ref(false);

const form = ref({
  email: "",
  password: "",
});

const submitLogin = async () => {
  isButtonDisabled.value = true;
  await useAuth.getToken();
  await axios
    .post("/login", {
      email: form.value.email,
      password: form.value.password,
    })
    .then((response) => {
      if (response.status === 204) {
        toast.success("Logged in successfully");
        router.push("/");
      }
    })
    .catch((error) => {
      toast.error(error.response.data.message);
    });
  isButtonDisabled.value = false;
};
</script>

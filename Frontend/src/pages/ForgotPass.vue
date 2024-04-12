<template>
    <div class="flex min-h-screen flex-1 flex-col justify-center px-6 py-12 lg:px-8">
        <div class="bg-gray-100 w-1/3 mx-auto p-4 rounded-xl shadow-md">
            <div class="grid grid-cols-3 items-center -mb-4">
                <router-link to="/login" class="flex items-center justify-self-start">
                    <FontAwesomeIcon :icon="['fas', 'arrow-left']" class="ml-2 mt-4 text-blue-600 hover:text-blue-500"
                        size="2x" />
                </router-link>
                <div class="justify-self-center">
                    <img src="/FRContent.svg" alt="FRContent Logo" class="h-16 w-auto" />
                </div>
                <div></div>
            </div>
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    Reset Password
                </h2>
            </div>

            <div class="flex justify-center">
                <span class="text-green-500 text-sm">
                    {{ message }}
                </span>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" @submit.prevent="submitForgotPass">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email
                            address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email"
                                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="form.email" />
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { authStore } from "../stores/authstore";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';

import axios from "axios";

library.add(fas);

const router = useRouter();
const useAuth = authStore();

const form = ref({
    email: ""
});

const message = ref();

const submitForgotPass = async () => {
    await useAuth.getToken();
    await axios
        .post("/forgot-password", {
            email: form.value.email
        })
        .then((response) => {
            if (response.status === 200) {
                message.value = response.data.status;
            }
        })

        .catch((error) => {
            console.log = error.response.data.message;
        });
};
</script>

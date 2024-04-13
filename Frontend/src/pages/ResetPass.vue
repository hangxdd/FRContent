<template>
    <div class="flex min-h-screen flex-1 flex-col justify-center px-6 py-12 lg:px-8">
        <div class="bg-gray-100 w-1/3 mx-auto p-4 rounded-xl shadow-md">
            <div class="text-center -mb-4">
                <img src="/FRContent.svg" alt="FRContent Logo" class="mx-auto h-16 w-auto">
            </div>
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    Reset your password
                </h2>
            </div>

            <div class="flex justify-center">
                <span class="text-green-500 text-sm">
                    {{ message }}
                </span>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" @submit.prevent="submitResetPass">
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="password"
                                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="form.password" />
                        </div>
                    </div>

                    <div>
                        <label for="confirm-password" class="block text-sm font-medium leading-6 text-gray-900">Confirm
                            Password</label>
                        <div class="mt-2">
                            <input id="confirm-password" name="confirm-password" type="password"
                                autocomplete="confirm-password"
                                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                v-model="form.password_confirmation" />
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { authStore } from "../stores/authstore";

import axios from "axios";

const router = useRouter();
const route = useRoute();
const useAuth = authStore();

const form = ref({
    token: route.params.token,
    email: route.query.email,
    password: "",
    password_confirmation: ""
});

const message = ref();

const submitResetPass = async () => {
    await useAuth.getToken();
    await axios
        .post("/reset-password", {
            token: form.value.token,
            email: form.value.email,
            password: form.value.password,
            password_confirmation: form.value.password_confirmation
        })
        .then((response) => {
            if (response.status === 200) {
                message.value = response.data.status;
                setTimeout(() => {
                    router.push("/login");
                }, 1500);
            }
        })

        .catch((error) => {
            console.log = error.response.data.message;
        });
};
</script>

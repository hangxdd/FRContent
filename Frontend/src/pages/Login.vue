<template>
    <div class="flex min-h-screen flex-1 flex-col justify-center px-6 py-12 lg:px-8">
        <div class="bg-gray-100 w-1/3 mx-auto p-4 rounded-xl shadow-md">
            <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                    Sign in to your account
                </h2>
            </div>

            <div class="flex justify-center">
                <span class="text-red-500 text-sm">
                    {{ errmessage }}
                </span>
            </div>

            <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                <form class="space-y-6" @submit.prevent="submitLogin">
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
                        <div class="flex items-center justify-between">
                            <label for="password"
                                class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="text-sm">
                                <router-link to="/forgotpass"
                                    class="font-semibold text-blue-600 hover:text-blue-500">Forgot
                                    password?</router-link>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                class="block w-full rounded-md border-0 py-1.5 indent-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                v-model="form.password" />
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-md bg-blue-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Sign in
                        </button>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-gray-500">
                    Not a member?
                    {{ " " }}
                    <router-link to="register"
                        class="font-semibold leading-6 text-blue-600 hover:text-blue-500">Register</router-link>
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { authStore } from "../stores/authstore";

import axios from "axios";

const router = useRouter();
const useAuth = authStore();

const form = ref({
    email: "",
    password: "",
});

const errmessage = ref();

const submitLogin = async () => {
    await useAuth.getToken();
    await axios
        .post("/login", {
            email: form.value.email,
            password: form.value.password,
        })
        .then((response) => {
            if (response.status === 204) {
                router.push("/");
            }
            errmessage.value = response.data.message;
        })

        .catch((error) => {
            console.log = error.response.data.message;
        });
};
</script>

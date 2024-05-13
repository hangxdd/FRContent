<template>
  <Disclosure as="nav" class="bg-gray-100 shadow-md sticky top-0 z-50" v-slot="{ open }">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
      <div class="relative flex h-16 items-center justify-between">
        <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
          <!-- Mobile menu button-->
          <DisclosureButton
            class="relative inline-flex items-center justify-center rounded-md p-2 text-black hover:bg-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
          >
            <span class="absolute -inset-0.5" />
            <span class="sr-only">Open main menu</span>
            <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
          </DisclosureButton>
        </div>
        <div
          class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start"
        >
          <div class="hidden sm:ml-6 sm:block">
            <div class="flex space-x-4">
              <router-link
                v-for="item in navigation"
                :key="item.name"
                :to="item.href"
                :class="[
                  item.current
                    ? 'bg-blue-600 text-white shadow-md'
                    : 'text-gray-400 hover:bg-gray-400 hover:text-white',
                  'rounded-md px-3 py-2 text-sm font-medium',
                ]"
                :aria-current="item.current ? 'page' : undefined"
                >{{ item.name }}</router-link
              >
            </div>
          </div>
        </div>
        <div
          class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0"
        >
          <button
            v-if="useAuth.user"
            type="button"
            class="relative rounded-full shadow-md bg-blue-700 hover:bg-blue-600 p-1 border-2 border-gray-200 hover:border-white text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-white"
          >
            <span class="absolute -inset-1.5" />
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 18v-5.25m0 0a6.01 6.01 0 0 0 1.5-.189m-1.5.189a6.01 6.01 0 0 1-1.5-.189m3.75 7.478a12.06 12.06 0 0 1-4.5 0m3.75 2.383a14.406 14.406 0 0 1-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 1 0-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"
              />
            </svg>
          </button>

          <!-- Profile dropdown -->
          <Menu as="div" class="relative ml-3">
            <div v-if="useAuth.user">
              <MenuButton
                class="relative flex rounded-full shadow-md bg-gray-200 hover:bg-white p-0.5 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-white"
              >
                <span class="absolute -inset-1.5" />
                <span class="sr-only">Open user menu</span>
                <img
                  class="h-8 w-8 rounded-full"
                  src="https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg"
                  alt=""
                />
              </MenuButton>
            </div>

            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <MenuItem v-slot="{ active }">
                  <router-link
                    to="/profile"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700',
                    ]"
                    >Your Profile</router-link
                  >
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <router-link
                    to="/settings"
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block px-4 py-2 text-sm text-gray-700',
                    ]"
                    >Settings</router-link
                  >
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <button
                    :class="[
                      active ? 'bg-gray-100' : '',
                      'block w-full px-4 py-2 text-sm text-gray-700 text-left',
                    ]"
                    @click="logOutAndRedirect"
                  >
                    Sign out
                  </button>
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </div>

    <DisclosurePanel
      class="absolute top-16 inset-x-0 p-2 transition transform origin-top-right md:hidden"
    >
      <div
        class="rounded-lg shadow-md bg-white ring-1 ring-black ring-opacity-5 overflow-hidden"
      >
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link
            v-for="item in navigation"
            :key="item.name"
            :to="item.href"
            :aria-current="item.current ? 'page' : undefined"
            class="block rounded-md px-3 py-2 text-base font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-50"
          >
            {{ item.name }}
          </router-link>
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
</template>

<script setup>
import { computed } from "vue";
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { Bars3Icon, BellIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { authStore } from "../stores/authstore";
import { useRouter, useRoute } from "vue-router";
import { useToast } from "vue-toastification";

const useAuth = authStore();
const router = useRouter();
const route = useRoute();
const toast = useToast();

const logOutAndRedirect = () => {
  toast("You have been logged out");
  useAuth.logOut();
  router.push("/login");
};

// Use a computed property for the navigation array
const navigation = computed(() => [
  { name: "Home", href: "/", current: route.path === "/" },
  { name: "Detect", href: "/detect", current: route.path === "/detect" },
  { name: "FAQ", href: "/faq", current: route.path === "/faq" },
  { name: "About", href: "/about", current: route.path === "/about" },
]);
</script>

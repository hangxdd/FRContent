<template>
  <div class="w-full px-4 pt-16">
    <div class="mx-auto w-full max-w-lg rounded-2xl bg-white p-2 shadow-md">
      <Disclosure v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>How does the emotion-based movie recommendation work?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          Our system uses advanced AI to detect your current emotion and recommends movies
          that match your mood. For instance, if you're feeling happy, we might suggest a
          comedy or a feel-good movie.
        </DisclosurePanel>
      </Disclosure>
      <Disclosure as="div" class="mt-2" v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>What emotions can the system detect?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          Our system can detect a range of emotions including happiness, sadness, anger,
          surprise, fear, and neutral.
        </DisclosurePanel>
      </Disclosure>
      <Disclosure as="div" class="mt-2" v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>What if I don't want to watch a movie based on my current emotion?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          You can click the button with the lightbulb icon besides your profile to go to
          the "Generate a random movie" page and watch a movie that is not based on your
          current emotion.
        </DisclosurePanel>
      </Disclosure>
      <Disclosure as="div" class="mt-2" v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>Is my emotional data stored or shared?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          No, we respect your privacy. Your emotional data is only used to recommend
          movies during your current session and is not stored or shared with any third
          parties.
        </DisclosurePanel>
      </Disclosure>
      <Disclosure as="div" class="mt-2" v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>Can I save a movie recommendation to watch later?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          Yes, you can add any movie recommendation to your "Favourites" tab for future
          viewing by clicking the "Favourite" button inside the movie's expanded card.
          <br />
          <br />
          Also, all of your recommended movies get automatically saved to the "History"
          tab.
        </DisclosurePanel>
      </Disclosure>
      <Disclosure as="div" class="mt-2" v-slot="{ open }">
        <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-blue-100 px-4 py-4 text-left text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500/75"
        >
          <span>What types of movies are in the recommendation pool?</span>
          <ChevronUpIcon
            :class="open ? 'rotate-180 transform' : ''"
            class="h-5 w-5 text-blue-500"
          />
        </DisclosureButton>
        <DisclosurePanel class="px-4 pb-2 pt-4 text-sm text-gray-500">
          Our movie pool includes a wide range of genres from classics to new releases,
          ensuring we have something for every mood and preference.
        </DisclosurePanel>
      </Disclosure>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { authStore } from "../stores/authstore";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { ChevronUpIcon } from "@heroicons/vue/20/solid";

const useAuth = authStore();

onMounted(async () => {
  await useAuth.getUser();
});
</script>

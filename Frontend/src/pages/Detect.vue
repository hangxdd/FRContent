<template>
  <div class="w-full max-w-md px-2 py-16 sm:px-0">
    <TabGroup>
      <TabList class="flex space-x-1 rounded-xl bg-blue-900/20 p-1">
        <Tab
          v-for="category in Object.keys(categories)"
          as="template"
          :key="category"
          v-slot="{ selected }"
        >
          <button
            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-600 shadow'
                : 'text-white hover:bg-white/[0.3] hover:text-white',
            ]"
          >
            {{ category }}
          </button>
        </Tab>
      </TabList>
      <TabPanels class="mt-2">
        <TabPanel v-for="(category, idx) in Object.keys(categories)" :key="idx">
          <!-- Check if it's the first tab -->
          <template v-if="category === 'Detect'">
            <div class="shadow-lg rounded-lg p-4">
              <video
                id="myVideo"
                class="w-full rounded"
                autoplay
                muted
                playsinline
                v-show="isPlaying"
              ></video>
              <div class="flex flex-col justify-between mt-4">
                <button
                  @click="toggleVideo"
                  :class="[
                    'font-bold py-2 px-4 rounded mb-2',
                    isPlaying
                      ? 'bg-red-600 hover:bg-red-500'
                      : 'bg-blue-600 hover:bg-blue-500',
                    'text-white',
                  ]"
                >
                  {{ isPlaying ? "Stop Video" : "Start Video" }}
                </button>
                <button
                  v-show="isPlaying"
                  class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded"
                >
                  Capture Emotion
                </button>
              </div>
            </div>
          </template>

          <!-- Content for Recommendations tab -->
          <template v-else-if="category === 'Recommendations'">
            <div>
              <!-- Your Recommendations content goes here -->
            </div>
          </template>

          <!-- Content for History tab -->
          <template v-else-if="category === 'History'">
            <div>
              <!-- Your History content goes here -->
            </div>
          </template>
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </div>
</template>

<script setup>
import * as faceAPI from "face-api.js";
import { onMounted, ref } from "vue";
import { authStore } from "../stores/authstore";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";

const useAuth = authStore();
const videoRef = ref(null);
const isPlaying = ref(false);

const categories = ref({
  Detect: [],
  Recommendations: [],
  History: [],
});

onMounted(async () => {
  useAuth.getUser();
});

const toggleVideo = async () => {
  const videoElement = document.getElementById("myVideo");
  if (isPlaying.value) {
    const tracks = videoElement.srcObject.getTracks();
    tracks.forEach((track) => track.stop());
    isPlaying.value = false;
  } else {
    try {
      const constraints = { video: true };
      const stream = await navigator.mediaDevices.getUserMedia(constraints);
      videoElement.srcObject = stream;
      videoElement.onloadedmetadata = () => {
        videoElement.play();
      };
      isPlaying.value = true;
    } catch (err) {
      console.error("Error: " + err);
    }
  }
};
</script>

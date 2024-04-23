<template>
  <div class="w-full max-w-md px-2 py-16 sm:px-0">
    <TabGroup>
      <TabList class="flex space-x-1 rounded-xl bg-blue-900/20 p-1">
        <Tab
          v-for="category in Object.keys(categories)"
          as="template"
          :key="category"
          v-slot="{ selected }"
          ref="tabs"
        >
          <button
            @click="activeTab = category"
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
              <div class="relative">
                <video
                  id="myVideo"
                  class="w-full rounded"
                  autoplay
                  muted
                  playsinline
                  v-show="isPlaying"
                ></video>
                <canvas
                  id="overlay"
                  class="absolute top-0 left-0 w-full h-full rounded"
                ></canvas>
              </div>
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
                  @click="captureEmotion"
                >
                  Capture Emotion
                </button>
              </div>
            </div>
          </template>

          <!-- Content for Recommendations tab -->
          <template v-else-if="category === 'Recommendations'">
            <div class="grid grid-cols-1 gap-4">
              <div
                v-for="(movie, index) in recommendedMovies"
                :key="index"
                class="bg-white rounded-lg shadow-md overflow-hidden"
              >
                <div
                  @click="movie.expanded = !movie.expanded"
                  class="cursor-pointer flex justify-between items-center p-4"
                >
                  <div class="flex items-center">
                    <img
                      :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                      alt="Movie poster"
                      class="w-16 h-16 object-cover mr-4"
                    />
                    <div>
                      <h2 class="font-bold text-xl mb-2">{{ movie.title }}</h2>
                      <p><strong>Release date:</strong> {{ movie.release_date }}</p>
                      <p><strong>Average vote:</strong> {{ movie.vote_average }}</p>
                    </div>
                  </div>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="h-6 w-6 transform transition-transform duration-200"
                    :class="{ 'rotate-180': movie.expanded }"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"
                    ></path>
                  </svg>
                </div>
                <div v-if="movie.expanded" class="p-4 border-t">
                  <p class="text-gray-700 text-base">{{ movie.overview }}</p>
                  <p class="mt-4">
                    <strong>Where to watch:</strong>
                    <a href="#" class="text-blue-500 hover:underline"
                      >Link to streaming service</a
                    >
                  </p>
                </div>
              </div>
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
import * as faceapi from "@vladmandic/face-api";
import { onBeforeUnmount, onMounted, ref, watchEffect } from "vue";
import { authStore } from "../stores/authstore";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";

const useAuth = authStore();
const isPlaying = ref(false);
const lastEmotion = ref(null);
const recommendedMovies = ref([]);

const activeTab = ref("Detect");

const tabs = ref(null);

const categories = ref({
  Detect: [],
  Recommendations: [],
  History: [],
});

onMounted(async () => {
  useAuth.getUser();
  await faceapi.nets.ssdMobilenetv1.loadFromUri("/models");
  await faceapi.nets.faceLandmark68Net.loadFromUri("/models");
  await faceapi.nets.faceExpressionNet.loadFromUri("/models");
});

onBeforeUnmount(() => {
  if (isPlaying.value) {
    toggleVideo();
  }
});

watchEffect(() => {
  if (activeTab.value !== "Detect" && isPlaying.value) {
    toggleVideo();
  }
});

const detectEmotions = async () => {
  const videoElement = document.getElementById("myVideo");
  const canvas = document.getElementById("overlay");

  // Check if the canvas element exists
  if (!canvas) {
    return;
  }

  const context = canvas.getContext("2d");
  context.clearRect(0, 0, canvas.width, canvas.height);

  const detections = await faceapi
    .detectAllFaces(videoElement, new faceapi.SsdMobilenetv1Options())
    .withFaceLandmarks()
    .withFaceExpressions();

  detections.forEach((detection) => {
    const { top, left, width, height } = detection.detection.box;
    const bestMatch = detection.expressions.asSortedArray()[0];
    lastEmotion.value = bestMatch.expression; // Update the last detected emotion

    context.beginPath();
    context.rect(left, top, width, height);
    context.lineWidth = 2;
    context.strokeStyle = "blue";
    context.fillStyle = "blue";
    context.stroke();
    context.font = "20px Arial";
    context.fillStyle = "white";
    context.fillText(bestMatch.expression, left, top > 20 ? top - 5 : 20);
  });

  if (isPlaying.value) {
    setTimeout(detectEmotions, 100);
  }
};

const getKeywords = async (emotion) => {
  try {
    const url = `https://api.themoviedb.org/3/search/keyword?query=${emotion}`;
    const options = {
      method: "GET",
      headers: {
        accept: "application/json",
        Authorization:
          "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNGYxM2UyZTE4MWJmMzM0ZDUxMGFiMzBjZDc5NTM1NyIsInN1YiI6IjY2MjEwMDVkODdhZTdiMDE0Y2Q3YmZiYyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.EiqnJa2IzQIaOuntxsikS0uVvaHMvX75lcPKVSLt3CQ",
      },
    };

    const response = await fetch(url, options);
    const json = await response.json();

    // Extract the keywords
    const keywords = json.results.map((result) => ({ id: result.id, name: result.name }));
    return keywords;
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const mapEmotionToKeywords = (emotion) => {
  const mappings = {
    neutral: ["documentary", "biography", "news", "reality-tv", "history"],
    // Add more mappings if needed
  };

  return mappings[emotion] || [emotion];
};

const captureEmotion = async () => {
  try {
    if (lastEmotion.value) {
      const mappedEmotions = mapEmotionToKeywords(lastEmotion.value);
      let keywords = [];
      for (const emotion of mappedEmotions) {
        const emotionKeywords = await getKeywords(emotion);
        keywords = [...keywords, ...emotionKeywords];
      }
      const keywordIds = keywords.map((keyword) => keyword.id);

      console.log(`Emotion: ${lastEmotion.value}`);
      console.log(`Keywords: ${keywords.map((keyword) => keyword.name).join(", ")}`);

      // Fetch the top 5 movies for each keyword
      const movies = [];
      for (const keywordId of keywordIds) {
        const url = `https://api.themoviedb.org/3/discover/movie?sort_by=vote_average.desc&with_keywords=${keywordId}&page=1&vote_count.gte=1000`;
        const options = {
          method: "GET",
          headers: {
            accept: "application/json",
            Authorization:
              "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNGYxM2UyZTE4MWJmMzM0ZDUxMGFiMzBjZDc5NTM1NyIsInN1YiI6IjY2MjEwMDVkODdhZTdiMDE0Y2Q3YmZiYyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.EiqnJa2IzQIaOuntxsikS0uVvaHMvX75lcPKVSLt3CQ",
          },
        };

        const response = await fetch(url, options);
        const json = await response.json();

        // Extract the top 5 movies
        const topMovies = json.results.slice(0, 6);
        movies.push(...topMovies);
      }

      // Sort the movies by vote average and take the top 5
      movies.sort((a, b) => b.vote_average - a.vote_average);
      recommendedMovies.value = movies
        .slice(0, 5)
        .map((movie) => ({ ...movie, expanded: false }));

      console.log(movies); // Corrected line

      // Find the index of the "Recommendations" tab
      const index = Object.keys(categories.value).indexOf("Recommendations");

      // Simulate a click event on the "Recommendations" tab
      tabs.value[index].$el.click();
    }
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const toggleVideo = async () => {
  const videoElement = document.getElementById("myVideo");
  const canvas = document.getElementById("overlay");
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
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
        detectEmotions();
      };
      isPlaying.value = true;
    } catch (err) {
      console.error("Error: " + err);
    }
  }
};
</script>

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
            <div
              v-if="recommendedMovies.length === 0"
              class="text-red-500 mb-4 font-bold text-2xl mt-10 text-center"
            >
              Detect your emotion first
            </div>
            <div v-else class="bg-white shadow-lg rounded-lg p-6 mx-2 my-8">
              <h2 class="text-2xl font-bold mb-2 text-primary-500">Recommended movies</h2>
              <p class="text-gray-600 mb-4">
                Based on the emotion: <strong>{{ emotionValue }}</strong>
              </p>
              <button
                class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded mb-4 flex justify-center items-center"
                @click="captureEmotion"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-6 h-6 mr-2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                  />
                </svg>
                New recommendations
              </button>
              <div class="grid grid-cols-1 gap-4">
                <div
                  v-for="(movie, index) in recommendedMovies"
                  :key="index"
                  class="bg-gray-100 rounded-lg shadow-md overflow-hidden transition-all duration-200"
                >
                  <div
                    @click="movie.expanded = !movie.expanded"
                    class="cursor-pointer flex justify-between items-center p-4"
                  >
                    <div class="flex items-center">
                      <img
                        :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                        alt="Movie poster"
                        class="w-16 h-16 object-cover mr-4 rounded"
                      />
                      <div>
                        <h2 class="font-bold text-xl mb-2 text-primary-500">
                          {{ movie.title }}
                        </h2>
                        <p class="text-gray-700">
                          <strong>Genres:</strong> {{ movie.genres.join(", ") }}
                        </p>
                        <p class="text-gray-700">
                          <strong>Release date:</strong> {{ movie.release_date }}
                        </p>
                        <p class="text-gray-700">
                          <strong>Average vote:</strong> {{ movie.vote_average }}
                        </p>
                      </div>
                    </div>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      class="h-6 w-6 transform transition-transform duration-200 text-primary-500"
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
                    <div class="mt-4">
                      <div
                        v-if="
                          movie.providers?.flatrate && movie.providers.flatrate.length > 0
                        "
                      >
                        <h4 class="mt-2 font-bold">Stream:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.flatrate"
                            :key="'flatrate-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div
                        v-if="movie.providers?.rent && movie.providers.rent.length > 0"
                      >
                        <h4 class="mt-2 font-bold">Rent:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.rent"
                            :key="'rent-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div v-if="movie.providers?.buy && movie.providers.buy.length > 0">
                        <h4 class="mt-2 font-bold">Buy:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.buy"
                            :key="'buy-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <p
                        v-if="
                          (!movie.providers?.flatrate ||
                            movie.providers.flatrate.length === 0) &&
                          (!movie.providers?.rent || movie.providers.rent.length === 0) &&
                          (!movie.providers?.buy || movie.providers.buy.length === 0)
                        "
                        class="italic mt-2"
                      >
                        No providers found...
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <!-- Content for History tab -->
          <template v-else-if="category === 'History'">
            <div
              v-if="!categories.History || categories.History.length === 0"
              class="text-red-500 text-center text-2xl font-bold mt-10 -mb-10"
            >
              No history movies found <br />
              may need to reload the page
            </div>
            <div
              v-if="categories.History && categories.History.length > 0"
              class="bg-white shadow-lg rounded-lg p-6 mx-2 my-8"
            >
              <h2 class="text-2xl font-bold mb-2 text-primary-500">History</h2>
              <p class="text-gray-600 mb-4">All your previously recommended movies.</p>
              <button
                v-if="categories.History && categories.History.length > 0"
                class="w-full bg-red-600 hover:bg-red-500 text-white font-bold p-2 mb-4 rounded flex justify-center items-center"
                @click="deleteAllHistoryMovies"
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
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                  />
                </svg>
                Delete All
              </button>
              <div class="grid grid-cols-1 gap-4">
                <div
                  v-for="(movie, index) in categories.History"
                  :key="index"
                  class="bg-gray-100 rounded-lg shadow-md overflow-hidden transition-all duration-200"
                >
                  <div
                    @click="movie.expanded = !movie.expanded"
                    class="cursor-pointer flex justify-between items-center p-4"
                  >
                    <div class="flex items-center">
                      <img
                        :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                        alt="Movie poster"
                        class="w-16 h-16 object-cover mr-4 rounded"
                      />
                      <div>
                        <h2 class="font-bold text-xl mb-2 text-primary-500">
                          {{ movie.title }}
                        </h2>
                        <p class="text-gray-700">
                          <strong>Genres:</strong> {{ movie.genres.join(", ") }}
                        </p>
                        <p class="text-gray-700">
                          <strong>Release date:</strong> {{ movie.release_date }}
                        </p>
                        <p class="text-gray-700">
                          <strong>Average vote:</strong> {{ movie.vote_average }}
                        </p>
                      </div>
                    </div>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      class="h-6 w-6 transform transition-transform duration-200 text-primary-500"
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
                    <div class="mt-4">
                      <div
                        v-if="
                          movie.providers?.flatrate && movie.providers.flatrate.length > 0
                        "
                      >
                        <h4 class="mt-2 font-bold">Stream:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.flatrate"
                            :key="'flatrate-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div
                        v-if="movie.providers?.rent && movie.providers.rent.length > 0"
                      >
                        <h4 class="mt-2 font-bold">Rent:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.rent"
                            :key="'rent-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div v-if="movie.providers?.buy && movie.providers.buy.length > 0">
                        <h4 class="mt-2 font-bold">Buy:</h4>
                        <ul class="flex flex-wrap">
                          <li
                            v-for="provider in movie.providers.buy"
                            :key="'buy-' + provider.provider_id"
                            class="mr-2 mb-1"
                          >
                            <a
                              :href="movie.providers.link"
                              target="_blank"
                              class="text-blue-500 hover:underline"
                            >
                              <img
                                :src="
                                  'https://image.tmdb.org/t/p/w500' + provider.logo_path
                                "
                                alt=""
                                class="w-12 h-auto rounded-md"
                              />
                            </a>
                          </li>
                        </ul>
                      </div>
                      <p
                        v-if="
                          (!movie.providers?.flatrate ||
                            movie.providers.flatrate.length === 0) &&
                          (!movie.providers?.rent || movie.providers.rent.length === 0) &&
                          (!movie.providers?.buy || movie.providers.buy.length === 0)
                        "
                        class="italic mt-2"
                      >
                        No providers found...
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </TabPanel>
      </TabPanels>
    </TabGroup>
  </div>
</template>

<script setup>
import * as faceapi from "@vladmandic/face-api";
import { onBeforeUnmount, onMounted, ref, watchEffect, computed } from "vue";
import { authStore } from "../stores/authstore";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import axios from "axios";

const useAuth = authStore();
const isPlaying = ref(false);
const lastEmotion = ref(null);
const recommendedEmotion = ref(null);
const recommendedMovies = ref([]);
const genres = ref([]);

const activeTab = ref("Detect");

const tabs = ref(null);

const categories = ref({
  Detect: [],
  Recommendations: [],
  History: [],
});

onMounted(async () => {
  await useAuth.getUser();
  await faceapi.nets.ssdMobilenetv1.loadFromUri("/models");
  await faceapi.nets.faceLandmark68Net.loadFromUri("/models");
  await faceapi.nets.faceExpressionNet.loadFromUri("/models");

  // Fetch the list of all genres
  const url = "https://api.themoviedb.org/3/genre/movie/list";
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
  genres.value = json.genres;

  await fetchUserHistoryMovies();
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

const fetchUserHistoryMovies = async () => {
  try {
    const userId = useAuth.user.id;
    const response = await axios.get(`/api/history_movies/${userId}`);
    const movieObjects = response.data;

    const moviePromises = movieObjects.map((movieObject) =>
      fetchMovieData(movieObject.movie_id)
    );
    const movies = await Promise.all(moviePromises);

    categories.value.History = movies;
  } catch (error) {
    console.error("Error fetching user history movies:", error);
  }
};

const fetchMovieData = async (movieId) => {
  const url = `https://api.themoviedb.org/3/movie/${movieId}`;
  const providersUrl = `https://api.themoviedb.org/3/movie/${movieId}/watch/providers`;
  const options = {
    method: "GET",
    headers: {
      accept: "application/json",
      Authorization:
        "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNGYxM2UyZTE4MWJmMzM0ZDUxMGFiMzBjZDc5NTM1NyIsInN1YiI6IjY2MjEwMDVkODdhZTdiMDE0Y2Q3YmZiYyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.EiqnJa2IzQIaOuntxsikS0uVvaHMvX75lcPKVSLt3CQ",
    },
  };

  const [movieResponse, providersResponse] = await Promise.all([
    fetch(url, options),
    fetch(providersUrl, options),
  ]);

  const movie = await movieResponse.json();
  const providers = await providersResponse.json();

  return {
    ...movie,
    genres: movie.genres.map((genre) => genre.name),
    providers: providers.results.US, // assuming you want providers for the US
  };
};

const deleteAllHistoryMovies = async () => {
  try {
    const userId = useAuth.user.id;
    await axios.delete(`/api/users/${userId}/history`);
    categories.value.History = [];
  } catch (error) {
    console.error("Error deleting user history movies:", error);
  }
};

const emotionValue = computed(() =>
  recommendedEmotion.value ? recommendedEmotion.value : "No emotion detected"
);

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
    surprised: [
      "thriller",
      "mystery",
      "horror",
      "sci-fi",
      "suspense",
      "crime",
      "action",
      "adventure",
      "fantasy",
      "drama",
    ],
    fearful: [
      "horror",
      "thriller",
      "mystery",
      "suspense",
      "crime",
      "drama",
      "sci-fi",
      "dark",
      "ghost",
      "zombie",
      "supernatural",
      "haunted",
      "alien",
      "monster",
      "apocalyptic",
      "disaster",
      "psychological",
      "disturbing",
      "gore",
      "slasher",
    ],
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

      // Randomly select a few keywords
      const selectedKeywords = [];
      while (selectedKeywords.length < 3 && keywords.length > 0) {
        const randomIndex = Math.floor(Math.random() * keywords.length);
        selectedKeywords.push(keywords[randomIndex]);
        keywords.splice(randomIndex, 1); // Remove the selected keyword
      }

      const keywordIds = selectedKeywords.map((keyword) => keyword.id);

      console.log(`Emotion: ${lastEmotion.value}`);
      console.log(
        `Keywords: ${selectedKeywords.map((keyword) => keyword.name).join(", ")}`
      );

      recommendedEmotion.value = lastEmotion.value;

      // Fetch the top 3 movies for each keyword in parallel
      const moviePromises = keywordIds.map((keywordId) => fetchTopMovie(keywordId));
      const movies = (await Promise.all(moviePromises)).flat();

      // Sort the movies by vote average and take the top 3
      movies.sort((a, b) => b.vote_average - a.vote_average);
      recommendedMovies.value = movies
        .slice(0, 3)
        .map((movie) => ({ ...movie, expanded: false }));

      console.log(movies);

      // Find the index of the "Recommendations" tab
      const index = Object.keys(categories.value).indexOf("Recommendations");

      // Simulate a click event on the "Recommendations" tab
      tabs.value[index].$el.click();
    }

    const userId = useAuth.user.id;
    const movieIds = recommendedMovies.value.map((movie) => movie.id);

    movieIds.forEach((movieId) => {
      axios
        .post("/api/history_movies", {
          user_id: userId,
          movie_id: movieId,
        })
        .then((response) => {
          console.log(response.data);
        })
        .catch((error) => {
          console.error(error);
        });
    });
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};

const fetchTopMovie = async (keywordId) => {
  const url = `https://api.themoviedb.org/3/discover/movie?sort_by=vote_average.desc&with_keywords=${keywordId}&page=1`;
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

  const topMovies = json.results.slice(0, 3).map(async (movie) => {
    const providersUrl = `https://api.themoviedb.org/3/movie/${movie.id}/watch/providers`;
    const providersResponse = await fetch(providersUrl, options);
    const providersJson = await providersResponse.json();

    return {
      ...movie,
      genres: movie.genre_ids.map(
        (id) => genres.value.find((genre) => genre.id === id)?.name || "Unknown"
      ),
      providers: providersJson.results.US, // Change 'US' to your desired country code
    };
  });

  return Promise.all(topMovies);
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

<template>
  <div v-if="!useAuth.user" class="flex justify-center items-center h-screen">
    <h3 class="text-3xl text-red-500 font-bold">No data</h3>
  </div>
  <div v-if="useAuth.user" class="w-full max-w-lg m-10 sm:px-0">
    <div class="bg-white shadow-lg rounded-lg p-6 mx-2 my-8">
      <h2 class="text-2xl font-bold mb-2 text-primary-500">Random movie generation</h2>
      <p class="text-gray-600 mb-4">
        Can't decide what to watch? Let us pick a movie for you!
      </p>
      <button
        class="w-full bg-green-600 hover:bg-green-500 text-white disabled:bg-gray-400 disabled:text-gray-200 font-semi py-2 px-4 rounded mb-4 flex justify-center items-center duration-200"
        :disabled="isButtonDisabled"
        @click="generateRandomMovie"
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
        Generate a movie
      </button>
      <div
        class="bg-gray-100 rounded-lg shadow-md overflow-hidden transition-all duration-200"
      >
        <div
          v-if="movieData"
          class="cursor-pointer flex justify-between items-center p-4"
          @click="expanded = !expanded"
        >
          <div class="flex flex-col md:flex-row items-center">
            <img
              :src="'https://image.tmdb.org/t/p/w500' + movieData.poster_path"
              class="w-24 object-cover mr-4 rounded"
            />
            <div>
              <h2 class="font-bold text-xl mb-2 text-primary-500">
                {{ movieData.title }}
              </h2>
              <p class="text-gray-700" v-if="movieData && movieData.genres">
                <strong>Genres:</strong>
                {{ movieData.genres.map((genre) => genre.name).join(", ") }}
              </p>
              <p class="text-gray-700">
                <strong>Release date:</strong> {{ movieData.release_date }}
              </p>
              <p class="text-gray-700">
                <strong>Average vote:</strong>
                {{ parseFloat(movieData.vote_average).toFixed(1) }}
              </p>
            </div>
          </div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            class="h-6 w-6 transform transition-transform duration-200 text-primary-500"
            :class="{ 'rotate-180': movieData.expanded }"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            ></path>
          </svg>
        </div>
        <div v-if="expanded" class="p-4 border-t">
          <p class="text-base" v-if="movieData.overview" v-html="movieData.overview"></p>
          <p class="text-red-500 text-base" v-else>
            <i>No overview available...</i>
          </p>
          <div
            v-if="movieData.trailer && movieData.trailer.length > 0"
            class="mt-4 relative"
          >
            <div
              class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-90 flex flex-col items-center justify-center z-10 hover:opacity-0 opacity-100 transition-opacity duration-500 hover:pointer-events-none"
            >
              <h4 class="mt-2 text-4xl font-bold text-gray-300">Trailer</h4>
              <p class="font-bold text-gray-300">Click to play</p>
            </div>
            <div style="padding-bottom: 56.25%">
              <iframe
                class="absolute top-0 left-0 w-full h-full z-0"
                :src="'https://www.youtube.com/embed/' + movieData.trailer"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>
          <p v-else class="mt-4 italic text-red-500">No trailer available...</p>
          <div class="mt-4">
            <button
              class="mt-2 p-2 font-semi text-center text-white bg-purple-600 hover:bg-purple-500 rounded w-full flex items-center justify-between"
              @click="showProviders = !showProviders"
            >
              <div class="flex items-center justify-center space-x-2 flex-grow">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-6 h-6 ml-4 md:ml-8 mr-2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M21 7.5V18M15 7.5V18M3 16.811V8.69c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061A1.125 1.125 0 0 1 3 16.811Z"
                  />
                </svg>

                Providers
              </div>

              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="h-6 w-6 mr-2 transform transition-transform duration-200 text-primary-500 hidden md:block"
                :class="{ 'rotate-180': showProviders }"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                ></path>
              </svg>
            </button>
          </div>
          <div v-show="showProviders">
            <div
              v-if="
                movieData.providers &&
                movieData.providers.US &&
                Object.keys(movieData.providers.US).length > 0
              "
            >
              <div
                v-for="[providerTypeName, providerType] in Object.entries(
                  movieData.providers.US
                )"
                :key="providerTypeName"
              >
                <div
                  v-if="
                    ['stream', 'rent', 'buy', 'flatrate', 'free', 'ads'].includes(
                      providerTypeName
                    ) && providerTypeName !== 'link'
                  "
                >
                  <h3 class="mt-2 font-bold">
                    {{
                      providerTypeName.charAt(0).toUpperCase() + providerTypeName.slice(1)
                    }}
                  </h3>
                  <ul
                    v-if="providerType && providerType.length > 0"
                    class="flex flex-wrap"
                  >
                    <li
                      v-for="(provider, index) in providerType"
                      :key="providerTypeName + '-' + index"
                      class="mr-2 mb-1"
                    >
                      <a
                        :href="movieData.providerLinks[provider.provider_id]"
                        target="_blank"
                      >
                        <img
                          :src="'https://image.tmdb.org/t/p/w500' + provider.logo_path"
                          alt=""
                          class="w-12 h-auto rounded-md"
                        />
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div v-else>
              <p class="text-red-500 italic mt-2">No providers available...</p>
            </div>
          </div>
          <div class="mt-4">
            <button
              @click="showActors = !showActors"
              class="mt-2 p-2 font-semibold text-center text-white bg-blue-600 hover:bg-blue-500 rounded w-full flex items-center justify-between"
            >
              <div class="flex items-center justify-center space-x-2 flex-grow">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="w-6 h-6 md:ml-6 mr-2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
                  />
                </svg>
                Actors
              </div>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="h-6 w-6 mr-2 transform transition-transform duration-200 text-primary-500 hidden md:block"
                :class="{ 'rotate-180': showActors }"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                ></path>
              </svg>
            </button>
            <div v-show="showActors">
              <div v-if="movieData.actors && movieData.actors.length > 0">
                <div class="relative w-full">
                  <div class="flex overflow-x-auto space-x-8 mt-4 p-2">
                    <div
                      v-for="actor in movieData.actors"
                      :key="'actor-' + actor.id"
                      class="flex flex-col items-center shadow-md rounded-md bg-white"
                    >
                      <a :href="actor.link" target="_blank">
                        <div class="flex w-32 h-40 overflow-hidden rounded-md">
                          <img
                            v-if="actor.profile_path"
                            :src="'https://image.tmdb.org/t/p/w500' + actor.profile_path"
                            alt=""
                            class="w-full h-full object-cover object-center"
                          />
                          <div
                            v-else
                            class="flex items-center justify-center text-red-500 text-center text-md font-bold"
                          >
                            Image Not Available
                          </div>
                        </div>
                        <div class="text-md font-bold p-4">
                          {{ actor.name }}
                        </div>
                        <div class="text-md p-4 -mt-8">
                          {{ actor.character }}
                        </div>
                      </a>
                    </div>
                  </div>
                  <div
                    class="absolute rounded top-0 right-0 bottom-0 w-20 bg-gradient-to-r from-transparent to-white"
                    style="pointer-events: none"
                  ></div>
                </div>
              </div>
              <p
                v-if="!movieData.actors || movieData.actors.length === 0"
                class="italic mt-2 text-red-500"
              >
                No actors data available...
              </p>
            </div>
            <button
              class="w-full text-white font-semibold p-2 mt-4 rounded flex justify-center items-center duration-200"
              :class="
                isFavourited[movieData.id]
                  ? 'bg-yellow-500 hover:bg-yellow-400'
                  : 'bg-yellow-300 hover:bg-yellow-200'
              "
              @click="toggleFavouriteMovie(movieData)"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                :fill="isFavourited[movieData.id] ? 'white' : 'none'"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-6 h-6 mr-1"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
                />
              </svg>
              {{ isFavourited[movieData.id] ? "Unfavourite" : "Favourite" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from "vue";
import { authStore } from "../stores/authstore";
import axios from "axios";
import { useToast } from "vue-toastification";

const useAuth = authStore();
const isButtonDisabled = ref(false);
const toast = useToast();
const movieData = ref(null);
const expanded = ref(false);
const showProviders = ref(false);
const showActors = ref(false);
let isFavourited = reactive({});

onMounted(async () => {
  await useAuth.getUser();
});

const generateRandomMovie = async () => {
  isButtonDisabled.value = true;
  const options = {
    method: "GET",
    headers: {
      accept: "application/json",
      Authorization:
        "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNGYxM2UyZTE4MWJmMzM0ZDUxMGFiMzBjZDc5NTM1NyIsInN1YiI6IjY2MjEwMDVkODdhZTdiMDE0Y2Q3YmZiYyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.EiqnJa2IzQIaOuntxsikS0uVvaHMvX75lcPKVSLt3CQ",
    },
  };

  try {
    // Fetch the first page to get the total number of pages
    const response = await fetch(
      "https://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=false&language=en-US&page=1&sort_by=popularity.desc",
      options
    );
    const data = await response.json();
    const totalPages = Math.min(data.total_pages, 500);

    // Generate a random page number
    const randomPage = Math.floor(Math.random() * totalPages) + 1;

    // Fetch a random page
    const response2 = await fetch(
      `https://api.themoviedb.org/3/discover/movie?language=en-US&page=${randomPage}`,
      options
    );
    const data2 = await response2.json();

    // Check if data2.results is defined and has at least one element
    if (data2.results && data2.results.length > 0) {
      // Pick a random movie from the page
      const randomIndex = Math.floor(Math.random() * data2.results.length);
      movieData.value = data2.results[randomIndex]; // Assign the movie's data to the reactive reference

      // Fetch the genres for the movie
      const response3 = await fetch(
        `https://api.themoviedb.org/3/movie/${movieData.value.id}`,
        options
      );
      const data3 = await response3.json();
      movieData.value.genres = data3.genres; // Assign the genres to the movie data

      // Fetch the trailer for the movie
      const response4 = await fetch(
        `https://api.themoviedb.org/3/movie/${movieData.value.id}/videos`,
        options
      );
      const data4 = await response4.json();

      // Check if data4.results is defined and has at least one element
      if (data4.results && data4.results.length > 0) {
        // Pick the first video as the trailer
        movieData.value.trailer = data4.results[0].key;
      } else {
        console.log("No trailer found for the movie");
      }

      // Initialize an object to store the provider links
      movieData.value.providerLinks = {};

      // Fetch the providers for the movie
      const response5 = await fetch(
        `https://api.themoviedb.org/3/movie/${movieData.value.id}/watch/providers?region=US`,
        options
      );
      const data5 = await response5.json();

      // Check if data5.results is defined and has at least one element
      if (data5.results && Object.keys(data5.results.US).length > 0) {
        // Assign the providers to the movie data
        movieData.value.providers = data5.results;
        console.log(movieData.value.providers);

        // Loop through each provider section
        for (const section in movieData.value.providers.US) {
          // Check if the section is an array and it is not empty
          if (
            Array.isArray(movieData.value.providers.US[section]) &&
            movieData.value.providers.US[section].length > 0
          ) {
            // Create a link for each provider in the section
            movieData.value.providers.US[section].forEach((provider) => {
              const providerLink = `https://www.themoviedb.org/movie/${movieData.value.id}/watch?locale=US`;
              // Store the provider link in the object with the provider ID as the key
              movieData.value.providerLinks[provider.provider_id] = providerLink;
            });
          }
        }
      } else {
        console.log("No providers found for the movie");
      }

      // Fetch the actors for the movie
      const response6 = await fetch(
        `https://api.themoviedb.org/3/movie/${movieData.value.id}/credits`,
        options
      );
      const data6 = await response6.json();

      // Check if data6.cast is defined and has at least one element
      if (data6.cast && data6.cast.length > 0) {
        // Assign the actors to the movie data
        movieData.value.actors = data6.cast;
        console.log(movieData.value.actors);
      } else {
        console.log("No actors found for the movie");
      }

      toast.success("Random movie generated successfully");
      console.log(movieData.value);

      const userId = useAuth.user.id;

      // Add the movie to the user's history
      const historyMoviesPromise = axios
        .post("/api/history_movies", {
          user_id: userId,
          movie_id: movieData.value.id, // assuming movieData.value contains the movie id
        })
        .catch((error) => {
          console.error(error);
        });
      await historyMoviesPromise;
    } else {
      console.log("No movies found on the random page");
    }
  } catch (error) {
    console.error("An error occurred:", error);
  }
  isButtonDisabled.value = false;
};

const toggleFavouriteMovie = async () => {
  try {
    const userId = useAuth.user.id;
    const movieId = movieData.value.id;
    if (isFavourited[movieId]) {
      await axios.delete(`/api/favourite_movies/${userId}/${movieId}`);
      isFavourited[movieId] = false;
      toast.success(`Movie with id ${movieId} removed from favourites successfully`);
    } else {
      const response = await axios.get(`/api/is_movie_favourited/${userId}/${movieId}`);
      const isAlreadyFavourited = response.data.isFavourited;

      if (!isAlreadyFavourited) {
        await axios.post("/api/favourite_movies", { user_id: userId, movie_id: movieId });
        isFavourited[movieId] = true;
        toast.success(`Movie with id ${movieId} added to favourites successfully`);
      } else {
        await axios.delete(`/api/favourite_movies/${userId}/${movieId}`);
        isFavourited[movieId] = false;
        toast.success(`Movie with id ${movieId} removed from favourites successfully`);
      }
    }
  } catch (error) {
    console.error(`Error toggling favourite status for movie with id ${movieId}:`, error);
    toast.error(`Error toggling favourite status for movie with id ${movieId}`);
  }
};
</script>

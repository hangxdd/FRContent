<template>
  <div v-if="!useAuth.user" class="flex justify-center items-center h-screen">
    <h3 class="text-3xl text-red-500 font-bold">No data</h3>
  </div>
  <div v-if="useAuth.user" class="w-full max-w-lg m-10 sm:px-0">
    <TabGroup>
      <TabList class="flex flex-col md:flex-row rounded-xl bg-blue-900/20 p-1">
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
              'w-full rounded-lg py-2.5 px-1.5 text-sm font-medium leading-5',
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
                <div
                  v-show="isGenerating"
                  class="absolute top-0 left-0 w-full h-full rounded bg-gray-900 bg-opacity-80 flex items-center justify-center text-white text-2xl"
                >
                  Generating movies...
                </div>
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
                  :disabled="isButtonDisabled"
                  class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded disabled:bg-gray-400 disabled:text-gray-200 duration-200"
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
                class="w-full bg-green-600 hover:bg-green-500 text-white disabled:bg-gray-400 disabled:text-gray-200 font-bold py-2 px-4 rounded mb-4 flex justify-center items-center duration-200"
                :disabled="isButtonDisabled"
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
                    <div class="flex flex-col md:flex-row items-center">
                      <img
                        :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                        alt="Movie poster"
                        class="w-24 object-cover mr-4 rounded"
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
                          <strong>Average vote:</strong>
                          {{ parseFloat(movie.vote_average).toFixed(1) }}
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
                    <p
                      class="text-base"
                      v-if="movie.overview"
                      v-html="movie.overview"
                    ></p>
                    <p class="text-red-500 text-base" v-else>
                      <i>No overview available...</i>
                    </p>
                    <div
                      v-if="movie.trailers && movie.trailers.length > 0"
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
                          :src="'https://www.youtube.com/embed/' + movie.trailers[0].key"
                          frameborder="0"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen
                        ></iframe>
                      </div>
                    </div>
                    <p v-else class="mt-4 italic text-red-500">No trailer available...</p>
                    <div class="mt-4">
                      <button
                        @click="movie.showProviders = !movie.showProviders"
                        class="mt-2 p-2 font-bold text-center text-white bg-purple-600 hover:bg-purple-500 rounded w-full flex items-center justify-between"
                      >
                        <div class="flex items-center justify-center space-x-2 flex-grow">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 ml-2 md:ml-8 mr-2"
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
                          :class="{ 'rotate-180': movie.showProviders }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showProviders" class="dropdown">
                        <div
                          v-if="
                            movie.providers?.flatrate &&
                            movie.providers.flatrate.length > 0
                          "
                        >
                          <h4 class="mt-2 font-bold">Stream:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.flatrate"
                              :key="'flatrate-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                              <a :href="movie.providers.link" target="_blank">
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
                          v-if="movie.providers?.buy && movie.providers.buy.length > 0"
                        >
                          <h4 class="mt-2 font-bold">Buy:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.buy"
                              :key="'buy-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                            (!movie.providers?.rent ||
                              movie.providers.rent.length === 0) &&
                            (!movie.providers?.buy || movie.providers.buy.length === 0)
                          "
                          class="italic mt-2 text-red-500"
                        >
                          No providers available...
                        </p>
                      </div>
                    </div>
                    <div class="mt-4">
                      <button
                        @click="movie.showActors = !movie.showActors"
                        class="mt-2 p-2 font-bold text-center text-white bg-blue-600 hover:bg-blue-500 rounded w-full flex items-center justify-between"
                      >
                        <div class="flex items-center justify-center space-x-2 flex-grow">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 ml-2 md:ml-8 mr-2"
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
                          :class="{ 'rotate-180': movie.showActors }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showActors" class="dropdown">
                        <div v-if="movie.actors && movie.actors.length > 0">
                          <div class="relative w-full">
                            <div class="flex overflow-x-auto space-x-8 mt-4 p-2">
                              <div
                                v-for="actor in movie.actors"
                                :key="'actor-' + actor.id"
                                class="flex flex-col items-center shadow-md rounded-md bg-white"
                              >
                                <a :href="actor.link" target="_blank">
                                  <div class="flex w-32 h-40 overflow-hidden rounded-md">
                                    <img
                                      v-if="actor.profile_path"
                                      :src="
                                        'https://image.tmdb.org/t/p/w500' +
                                        actor.profile_path
                                      "
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
                          v-if="!movie.actors || movie.actors.length === 0"
                          class="italic mt-2 text-red-500"
                        >
                          No actors data available...
                        </p>
                      </div>
                    </div>
                    <button
                      class="w-full text-white font-bold p-2 mt-4 rounded flex justify-center items-center duration-200"
                      :class="
                        isFavourited[movie.id]
                          ? 'bg-yellow-500 hover:bg-yellow-400'
                          : 'bg-yellow-300 hover:bg-yellow-200'
                      "
                      @click="toggleFavouriteMovie(movie.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :fill="isFavourited[movie.id] ? 'white' : 'none'"
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
                      {{ isFavourited[movie.id] ? "Unfavourite" : "Favourite" }}
                    </button>
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
              <p class="text-gray-600 mb-2">All your previously recommended movies.</p>
              <p class="text-gray-600 mb-4">
                Movies fetched: <strong>{{ histMovieCount }}</strong>
              </p>
              <button
                v-if="categories.History && categories.History.length > 0"
                class="w-full bg-red-600 hover:bg-red-500 text-white font-bold p-2 mb-4 rounded flex justify-center items-center duration-200"
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
              <Menu as="div" class="w-full relative inline-block text-left">
                <div class="group">
                  <MenuButton
                    @click="isOpen = !isOpen"
                    class="inline-flex w-full justify-between rounded-md bg-blue-500 hover:bg-blue-400 mb-4 p-1 font-bold text-white"
                  >
                    <div class="flex-grow flex justify-center">
                      <div class="flex items-center">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="w-8 h-8 ml-6 mr-2"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25"
                          />
                        </svg>
                        Sort By
                      </div>
                    </div>
                    <div>
                      <ChevronDownIcon
                        class="transition-transform duration-200 mr-1 h-8 w-8 text-white"
                        aria-hidden="true"
                      />
                    </div>
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
                    class="absolute right-0 z-10 w-full origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <div class="py-1">
                      <MenuItem v-slot="{ active }">
                        <a
                          @click="() => sortByTitle('History')"
                          :class="[
                            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                            'w-full inline-flex px-4 py-2 text-sm items-center cursor-pointer',
                          ]"
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
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                            />
                          </svg>

                          Title
                        </a>
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <a
                          @click="() => sortByGenre('History')"
                          :class="[
                            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                            'w-full inline-flex px-4 py-2 text-sm items-center cursor-pointer',
                          ]"
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
                              d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 7.746 6 7.125v-1.5M4.875 8.25C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.125v-1.5m1.125 2.625c-.621 0-1.125.504-1.125 1.125v1.5m2.625-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-12 5.25v-5.25m0 5.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125m-12 0v-1.5c0-.621-.504-1.125-1.125-1.125M18 18.375v-5.25m0 5.25v-1.5c0-.621.504-1.125 1.125-1.125M18 13.125v1.5c0 .621.504 1.125 1.125 1.125M18 13.125c0-.621.504-1.125 1.125-1.125M6 13.125v1.5c0 .621-.504 1.125-1.125 1.125M6 13.125C6 12.504 5.496 12 4.875 12m-1.5 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M19.125 12h1.5m0 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h1.5m14.25 0h1.5"
                            />
                          </svg>
                          Genre
                        </a>
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <a
                          @click="() => sortByReleaseDate('History')"
                          :class="[
                            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                            'w-full inline-flex px-4 py-2 text-sm cursor-pointer',
                          ]"
                          ><svg
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
                              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"
                            />
                          </svg>
                          Release Date</a
                        >
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <a
                          @click="() => sortByVoteAverage('History')"
                          :class="[
                            active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                            'w-full inline-flex px-4 py-2 text-sm cursor-pointer',
                          ]"
                          ><svg
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
                              d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"
                            />
                          </svg>
                          Average Vote</a
                        >
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
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
                    <div class="flex flex-col md:flex-row items-center">
                      <img
                        :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                        alt="Movie poster"
                        class="w-24 object-cover mr-4 rounded"
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
                          <strong>Average vote:</strong>
                          {{ parseFloat(movie.vote_average).toFixed(1) }}
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
                    <p
                      class="text-base"
                      v-if="movie.overview"
                      v-html="movie.overview"
                    ></p>
                    <p class="text-red-500 text-base" v-else>
                      <i>No overview available...</i>
                    </p>
                    <div
                      v-if="movie.trailers && movie.trailers.length > 0"
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
                          :src="'https://www.youtube.com/embed/' + movie.trailers[0].key"
                          frameborder="0"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen
                        ></iframe>
                      </div>
                    </div>
                    <p v-else class="mt-4 italic text-red-500">No trailer available...</p>
                    <div class="mt-4">
                      <button
                        @click="movie.showProviders = !movie.showProviders"
                        class="mt-2 p-2 font-bold text-center text-white bg-purple-600 hover:bg-purple-500 rounded w-full flex items-center justify-between"
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
                          :class="{ 'rotate-180': movie.showProviders }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showProviders" class="dropdown">
                        <div
                          v-if="
                            movie.providers?.flatrate &&
                            movie.providers.flatrate.length > 0
                          "
                        >
                          <h4 class="mt-2 font-bold">Stream:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.flatrate"
                              :key="'flatrate-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                              <a :href="movie.providers.link" target="_blank">
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
                          v-if="movie.providers?.buy && movie.providers.buy.length > 0"
                        >
                          <h4 class="mt-2 font-bold">Buy:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.buy"
                              :key="'buy-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                            (!movie.providers?.rent ||
                              movie.providers.rent.length === 0) &&
                            (!movie.providers?.buy || movie.providers.buy.length === 0)
                          "
                          class="italic mt-2 text-red-500"
                        >
                          No providers available...
                        </p>
                      </div>
                    </div>
                    <div class="mt-4">
                      <button
                        @click="movie.showActors = !movie.showActors"
                        class="mt-2 p-2 font-bold text-center text-white bg-blue-600 hover:bg-blue-500 rounded w-full flex items-center justify-between"
                      >
                        <div class="flex items-center justify-center space-x-2 flex-grow">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 ml-2 md:ml-8 mr-2"
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
                          :class="{ 'rotate-180': movie.showActors }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showActors" class="dropdown">
                        <div v-if="movie.actors && movie.actors.length > 0">
                          <div class="relative w-full">
                            <div class="flex overflow-x-auto space-x-8 mt-4 p-2">
                              <div
                                v-for="actor in movie.actors"
                                :key="'actor-' + actor.id"
                                class="flex flex-col items-center shadow-md rounded-md bg-white"
                              >
                                <a :href="actor.link" target="_blank">
                                  <div class="flex w-36 h-40 rounded-md">
                                    <img
                                      v-if="actor.profile_path"
                                      :src="
                                        'https://image.tmdb.org/t/p/w500' +
                                        actor.profile_path
                                      "
                                      alt=""
                                      class="w-full h-full rounded object-cover object-center"
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
                          v-if="!movie.actors || movie.actors.length === 0"
                          class="italic mt-2 text-red-500"
                        >
                          No actors data available...
                        </p>
                      </div>
                    </div>
                    <button
                      class="w-full text-white font-bold p-2 mt-4 rounded flex justify-center items-center duration-200"
                      :class="
                        isFavourited[movie.id]
                          ? 'bg-yellow-500 hover:bg-yellow-400'
                          : 'bg-yellow-300 hover:bg-yellow-200'
                      "
                      @click="toggleFavouriteMovie(movie.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :fill="isFavourited[movie.id] ? 'white' : 'none'"
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
                      {{ isFavourited[movie.id] ? "Unfavourite" : "Favourite" }}
                    </button>
                    <button
                      class="w-full bg-red-600 hover:bg-red-500 text-white font-bold p-2 mt-4 rounded flex justify-center items-center duration-200"
                      @click="deleteHistoryMovie(movie.id)"
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
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </template>

          <template v-else-if="category === 'Favourites'">
            <div
              v-if="!categories.Favourites || categories.Favourites.length === 0"
              class="text-red-500 text-center text-2xl font-bold mt-10 -mb-10"
            >
              No favourited movies found <br />
              may need to reload the page
            </div>
            <div
              v-if="categories.Favourites && categories.Favourites.length > 0"
              class="bg-white shadow-lg rounded-lg p-6 mx-2 my-8"
            >
              <h2 class="text-2xl font-bold mb-2 text-primary-500">Favourites</h2>
              <p class="text-gray-600 mb-2">All your favourited movies.</p>
              <p class="text-gray-600 mb-4">
                Movies fetched: <strong>{{ favMovieCount }}</strong>
              </p>
              <div class="grid grid-cols-1 gap-4">
                <Menu as="div" class="w-full relative inline-block text-left">
                  <div class="group">
                    <MenuButton
                      @click="isOpen = !isOpen"
                      class="inline-flex w-full justify-between rounded-md bg-blue-500 hover:bg-blue-400 p-1 font-bold text-white"
                    >
                      <div class="flex-grow flex justify-center">
                        <div class="flex items-center">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-8 h-8 ml-6 mr-2"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25"
                            />
                          </svg>
                          Sort By
                        </div>
                      </div>
                      <div>
                        <ChevronDownIcon
                          class="transition-transform duration-200 mr-1 h-8 w-8 text-white"
                          aria-hidden="true"
                        />
                      </div>
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
                      class="absolute right-0 z-10 w-full origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                      <div class="py-1">
                        <MenuItem v-slot="{ active }">
                          <a
                            @click="() => sortByTitle('Favourites')"
                            :class="[
                              active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                              'w-full inline-flex px-4 py-2 text-sm items-center cursor-pointer',
                            ]"
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
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                              />
                            </svg>

                            Title
                          </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                          <a
                            @click="() => sortByGenre('Favourites')"
                            :class="[
                              active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                              'w-full inline-flex px-4 py-2 text-sm items-center cursor-pointer',
                            ]"
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
                                d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h1.5C5.496 19.5 6 18.996 6 18.375m-3.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-1.5A1.125 1.125 0 0 1 18 18.375M20.625 4.5H3.375m17.25 0c.621 0 1.125.504 1.125 1.125M20.625 4.5h-1.5C18.504 4.5 18 5.004 18 5.625m3.75 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 4.5c-.621 0-1.125.504-1.125 1.125M3.375 4.5h1.5C5.496 4.5 6 5.004 6 5.625m-3.75 0v1.5c0 .621.504 1.125 1.125 1.125m0 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m1.5-3.75C5.496 8.25 6 7.746 6 7.125v-1.5M4.875 8.25C5.496 8.25 6 8.754 6 9.375v1.5m0-5.25v5.25m0-5.25C6 5.004 6.504 4.5 7.125 4.5h9.75c.621 0 1.125.504 1.125 1.125m1.125 2.625h1.5m-1.5 0A1.125 1.125 0 0 1 18 7.125v-1.5m1.125 2.625c-.621 0-1.125.504-1.125 1.125v1.5m2.625-2.625c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125M18 5.625v5.25M7.125 12h9.75m-9.75 0A1.125 1.125 0 0 1 6 10.875M7.125 12C6.504 12 6 12.504 6 13.125m0-2.25C6 11.496 5.496 12 4.875 12M18 10.875c0 .621-.504 1.125-1.125 1.125M18 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m-12 5.25v-5.25m0 5.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125m-12 0v-1.5c0-.621-.504-1.125-1.125-1.125M18 18.375v-5.25m0 5.25v-1.5c0-.621.504-1.125 1.125-1.125M18 13.125v1.5c0 .621.504 1.125 1.125 1.125M18 13.125c0-.621.504-1.125 1.125-1.125M6 13.125v1.5c0 .621-.504 1.125-1.125 1.125M6 13.125C6 12.504 5.496 12 4.875 12m-1.5 0h1.5m-1.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M19.125 12h1.5m0 0c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h1.5m14.25 0h1.5"
                              />
                            </svg>
                            Genre
                          </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                          <a
                            @click="() => sortByReleaseDate('Favourites')"
                            :class="[
                              active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                              'w-full inline-flex px-4 py-2 text-sm cursor-pointer',
                            ]"
                            ><svg
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
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"
                              />
                            </svg>
                            Release Date</a
                          >
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                          <a
                            @click="() => sortByVoteAverage('Favourites')"
                            :class="[
                              active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                              'w-full inline-flex px-4 py-2 text-sm cursor-pointer',
                            ]"
                            ><svg
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
                                d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z"
                              />
                            </svg>
                            Average Vote</a
                          >
                        </MenuItem>
                      </div>
                    </MenuItems>
                  </transition>
                </Menu>
                <div
                  v-for="(movie, index) in categories.Favourites"
                  :key="index"
                  class="bg-gray-100 rounded-lg shadow-md overflow-hidden transition-all duration-200"
                >
                  <div
                    @click="movie.expanded = !movie.expanded"
                    class="cursor-pointer flex justify-between items-center p-4"
                  >
                    <div class="flex flex-col md:flex-row items-center">
                      <img
                        :src="'https://image.tmdb.org/t/p/w500' + movie.poster_path"
                        alt="Movie poster"
                        class="w-24 object-cover mr-4 rounded"
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
                          <strong>Average vote:</strong>
                          {{ parseFloat(movie.vote_average).toFixed(1) }}
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
                    <p
                      class="text-base"
                      v-if="movie.overview"
                      v-html="movie.overview"
                    ></p>
                    <p class="text-red-500 text-base" v-else>
                      <i>No overview available...</i>
                    </p>
                    <div
                      v-if="movie.trailers && movie.trailers.length > 0"
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
                          :src="'https://www.youtube.com/embed/' + movie.trailers[0].key"
                          frameborder="0"
                          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                          allowfullscreen
                        ></iframe>
                      </div>
                    </div>
                    <p v-else class="mt-4 italic text-red-500">No trailer available...</p>
                    <div class="mt-4">
                      <button
                        @click="movie.showProviders = !movie.showProviders"
                        class="mt-2 p-2 font-bold text-center text-white bg-purple-600 hover:bg-purple-500 rounded w-full flex items-center justify-between"
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
                          :class="{ 'rotate-180': movie.showProviders }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showProviders" class="dropdown">
                        <div
                          v-if="
                            movie.providers?.flatrate &&
                            movie.providers.flatrate.length > 0
                          "
                        >
                          <h4 class="mt-2 font-bold">Stream:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.flatrate"
                              :key="'flatrate-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                              <a :href="movie.providers.link" target="_blank">
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
                          v-if="movie.providers?.buy && movie.providers.buy.length > 0"
                        >
                          <h4 class="mt-2 font-bold">Buy:</h4>
                          <ul class="flex flex-wrap">
                            <li
                              v-for="provider in movie.providers.buy"
                              :key="'buy-' + provider.provider_id"
                              class="mr-2 mb-1"
                            >
                              <a :href="movie.providers.link" target="_blank">
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
                            (!movie.providers?.rent ||
                              movie.providers.rent.length === 0) &&
                            (!movie.providers?.buy || movie.providers.buy.length === 0)
                          "
                          class="italic mt-2 text-red-500"
                        >
                          No providers available...
                        </p>
                      </div>
                    </div>
                    <div class="mt-4">
                      <button
                        @click="movie.showActors = !movie.showActors"
                        class="mt-2 p-2 font-bold text-center text-white bg-blue-600 hover:bg-blue-500 rounded w-full flex items-center justify-between"
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
                          :class="{ 'rotate-180': movie.showActors }"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                          ></path>
                        </svg>
                      </button>
                      <div v-show="movie.showActors" class="dropdown">
                        <div v-if="movie.actors && movie.actors.length > 0">
                          <div class="relative w-full">
                            <div class="flex overflow-x-auto space-x-8 mt-4 p-2">
                              <div
                                v-for="actor in movie.actors"
                                :key="'actor-' + actor.id"
                                class="flex flex-col items-center shadow-md rounded-md bg-white"
                              >
                                <a :href="actor.link" target="_blank">
                                  <div class="flex w-32 h-40 overflow-hidden rounded-md">
                                    <img
                                      v-if="actor.profile_path"
                                      :src="
                                        'https://image.tmdb.org/t/p/w500' +
                                        actor.profile_path
                                      "
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
                          v-if="!movie.actors || movie.actors.length === 0"
                          class="italic mt-2 text-red-500"
                        >
                          No actors data available...
                        </p>
                      </div>
                    </div>
                    <button
                      class="w-full text-white font-bold p-2 mt-4 rounded flex justify-center items-center duration-200"
                      :class="
                        isFavourited[movie.id]
                          ? 'bg-yellow-500 hover:bg-yellow-400'
                          : 'bg-yellow-300 hover:bg-yellow-200'
                      "
                      @click="toggleFavouriteMovie(movie.id)"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :fill="isFavourited[movie.id] ? 'white' : 'none'"
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
                      {{ isFavourited[movie.id] ? "Unfavourite" : "Favourite" }}
                    </button>
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
import { onBeforeUnmount, onMounted, ref, watchEffect, computed, reactive } from "vue";
import { authStore } from "../stores/authstore";
import {
  TabGroup,
  TabList,
  Tab,
  TabPanels,
  TabPanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from "@headlessui/vue";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";
import axios from "axios";
import { useToast } from "vue-toastification";

// Refs
const isPlaying = ref(false);
const lastEmotion = ref(null);
const recommendedEmotion = ref(null);
const recommendedMovies = ref([]);
const isGenerating = ref(false);
const isOpen = ref(false);
const activeTab = ref("Detect");
const tabs = ref(null);
const isButtonDisabled = ref(false);

// Reactive
let isFavourited = reactive({});

// Computed
const histMovieCount = computed(() => categories.value.History.length);
const favMovieCount = computed(() => categories.value.Favourites.length);

// Stores and other services
const useAuth = authStore();
const toast = useToast();

// Categories
const categories = ref({
  Detect: [],
  Recommendations: [],
  History: [],
  Favourites: [],
});

onMounted(async () => {
  await useAuth.getUser();
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
  if (activeTab.value === "History") {
    fetchUserHistoryMovies();
  } else if (activeTab.value === "Favourites") {
    fetchFavouriteMovies();
  }
});

const sortByTitle = (category) => {
  if (categories.value[category] instanceof Array) {
    const sortedArray = [...categories.value[category]].sort((a, b) => {
      if (a.title > b.title) return 1;
      if (a.title < b.title) return -1;
      return 0;
    });
    categories.value[category] = sortedArray;
  }
};

const sortByGenre = (category) => {
  if (categories.value[category] instanceof Array) {
    const sortedArray = [...categories.value[category]].sort((a, b) => {
      if (a.genres > b.genres) return 1;
      if (a.genres < b.genres) return -1;
      return 0;
    });
    categories.value[category] = sortedArray;
  }
};

const sortByReleaseDate = (category) => {
  if (categories.value[category] instanceof Array) {
    const sortedArray = [...categories.value[category]].sort((a, b) => {
      if (a.release_date > b.release_date) return 1;
      if (a.release_date < b.release_date) return -1;
      return 0;
    });
    categories.value[category] = sortedArray;
  }
};

const sortByVoteAverage = (category) => {
  if (categories.value[category] instanceof Array) {
    const sortedArray = [...categories.value[category]].sort(
      (a, b) => b.vote_average - a.vote_average
    );
    categories.value[category] = sortedArray;
  }
};

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
    toast.success("User's history movies fetched successfully");
  } catch (error) {
    console.error("Error fetching user history movies:", error);
    toast.error("Error fetching user history movies");
  }
};

const fetchMovieData = async (movieId) => {
  const url = `https://api.themoviedb.org/3/movie/${movieId}?append_to_response=watch/providers,credits`;
  const options = {
    method: "GET",
    headers: {
      accept: "application/json",
      Authorization:
        "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIzNGYxM2UyZTE4MWJmMzM0ZDUxMGFiMzBjZDc5NTM1NyIsInN1YiI6IjY2MjEwMDVkODdhZTdiMDE0Y2Q3YmZiYyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.EiqnJa2IzQIaOuntxsikS0uVvaHMvX75lcPKVSLt3CQ",
    },
  };

  const response = await fetch(url, options);
  const movie = await response.json();

  return {
    ...movie,
    genres: movie.genres.map((genre) => genre.name),
    providers: movie["watch/providers"].results.US, // assuming you want providers for the US
    trailers: await fetchMovieTrailers(movieId, options), // keep this separate as it's not available in the append_to_response parameter
    showProviders: false,
    actors: movie.credits.cast, // assuming you want the cast of the movie
  };
};

const deleteAllHistoryMovies = async () => {
  try {
    const userId = useAuth.user.id;
    await axios.delete(`/api/users/${userId}/history`);
    categories.value.History = [];
    toast.success("User history movies deleted successfully");
  } catch (error) {
    console.error("Error deleting user history movies:", error);
    toast.error("Error deleting user history movies");
  }
};

const deleteHistoryMovie = async (movieId) => {
  try {
    const userId = useAuth.user.id;
    await axios.delete(`/api/history-movies/${userId}/${movieId}`);
    categories.value.History = categories.value.History.filter(
      (movie) => movie.id !== movieId
    );
    toast.success(`Movie with id ${movieId} deleted from user history successfully`);
  } catch (error) {
    console.error(`Error deleting movie with id ${movieId} from user history:`, error);
    toast.error(`Error deleting movie with id ${movieId} from user history`);
  }
};

const toggleFavouriteMovie = async (movieId) => {
  try {
    const userId = useAuth.user.id;
    if (isFavourited[movieId]) {
      await axios.delete(`/api/favourite_movies/${userId}/${movieId}`);
      isFavourited[movieId] = false;
      toast.success(`Movie with id ${movieId} removed from favourites successfully`);
    } else {
      // Check if the movie is already favorited by the user
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

const fetchFavouriteMovies = async () => {
  try {
    const userId = useAuth.user.id;
    const response = await axios.get(`/api/favourite_movies/${userId}`);
    const movieObjects = response.data;

    const moviePromises = movieObjects.map((movieObject) => {
      isFavourited[movieObject.movie_id] = true;
      return fetchMovieData(movieObject.movie_id);
    });

    const movies = await Promise.all(moviePromises);

    categories.value.Favourites = movies;
    toast.success("User's favourite movies fetched successfully");
  } catch (error) {
    console.error("Error fetching favourite movies:", error);
    toast.error("Error fetching favourite movies");
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
    happy: ["comedy", "romance", "family", "animation", "musical", "fantasy"],
    sad: ["drama", "romance", "biography", "history", "music", "war"],
    angry: ["action", "thriller", "crime", "war", "adventure", "drama"],
    fearful: [
      "horror",
      "thriller",
      "mystery",
      "suspense",
      "crime",
      "drama",
      "sci-fi",
      "dark",
    ],
    disgusted: ["horror", "thriller", "crime", "mystery"],
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
  };

  // If the emotion is in the mappings, return the mapped keywords
  if (emotion in mappings) {
    return mappings[emotion];
  }
  // If the emotion is not in the mappings, return the emotion as a keyword
  else {
    return [emotion];
  }
};

const captureEmotion = async () => {
  isButtonDisabled.value = true;
  isGenerating.value = true;
  const initialLastEmotion = lastEmotion.value;
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

      // Fetch the movies for each keyword in parallel
      const moviePromises = keywordIds.map((keywordId) => fetchMovies(keywordId));
      const movies = (await Promise.all(moviePromises)).flat();

      // Randomly select 3 unique movies
      const selectedMovies = [];
      while (selectedMovies.length < 3 && movies.length > 0) {
        const randomIndex = Math.floor(Math.random() * movies.length);
        const selectedMovie = movies[randomIndex];
        if (!selectedMovies.find((movie) => movie.id === selectedMovie.id)) {
          // Check if the movie has already been selected
          selectedMovies.push(selectedMovie);
        }
        movies.splice(randomIndex, 1); // Remove the selected movie
      }

      recommendedMovies.value = selectedMovies.map((movie) => ({
        ...movie,
        expanded: false,
      }));

      console.log(selectedMovies);

      // Find the index of the "Recommendations" tab
      const index = Object.keys(categories.value).indexOf("Recommendations");

      // Simulate a click event on the "Recommendations" tab
      tabs.value[index].$el.click();
    }

    const userId = useAuth.user.id;
    const movieIds = recommendedMovies.value.map((movie) => movie.id);

    const historyMoviesPromises = movieIds.map((movieId) => {
      return axios
        .post("/api/history_movies", {
          user_id: userId,
          movie_id: movieId,
        })
        .catch((error) => {
          console.error(error);
        });
    });

    await Promise.all(historyMoviesPromises);
    toast.success(`Generated recommended movies successfully`);
  } catch (error) {
    console.error("Error fetching data:", error);
    toast.error("Error fetching data");
  } finally {
    isGenerating.value = false;
    lastEmotion.value = initialLastEmotion;
    isButtonDisabled.value = false;
  }
};

const fetchMovies = async (keywordId) => {
  const url = `https://api.themoviedb.org/3/discover/movie?with_keywords=${keywordId}&page=1`;
  const genreUrl = `https://api.themoviedb.org/3/genre/movie/list`;
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

  const genreResponse = await fetch(genreUrl, options);
  const genreJson = await genreResponse.json();

  const genres = genreJson.genres;

  const movies = json.results.map(async (movie) => {
    const movieData = await fetchMovieData(movie.id);
    return {
      ...movie,
      genres: movie.genre_ids.map(
        (id) => genres.find((genre) => genre.id === id)?.name || "Unknown"
      ),
      providers: movieData.providers,
      trailers: movieData.trailers,
      actors: movieData.actors, // added actors property
    };
  });

  return Promise.all(movies);
};

const fetchMovieTrailers = async (movieId, options) => {
  const trailersUrl = `https://api.themoviedb.org/3/movie/${movieId}/videos`;
  const trailersResponse = await fetch(trailersUrl, options);
  const trailersJson = await trailersResponse.json();
  return trailersJson.results.filter((video) => video.type === "Trailer");
};

const toggleVideo = async () => {
  const videoElement = document.getElementById("myVideo");
  const canvas = document.getElementById("overlay");
  if (isPlaying.value) {
    const tracks = videoElement.srcObject.getTracks();
    tracks.forEach((track) => track.stop());
    isPlaying.value = false;
    toast.error("Video stopped");
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
      toast.success("Video started");
    } catch (err) {
      console.error("Error: " + err);
      toast.error("Error starting video: " + err);
    }
  }
};
</script>

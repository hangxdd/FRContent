import { createApp } from "vue"
import { createPinia } from "pinia"

import App from "./App.vue"
import router from "./router"

import "./index.css"
import "../axios.js"

const app = createApp(App)

// Create a Pinia instance
const pinia = createPinia()

// Use the router and Pinia in your application
app.use(router)
app.use(pinia)

// Mount the application
app.mount("#app")

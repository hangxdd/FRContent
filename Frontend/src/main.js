import { createApp } from "vue"
import { createPinia } from "pinia"

import App from "./App.vue"
import router from "./router"
import Toast, { POSITION } from 'vue-toastification';

import "./index.css"
import "../axios.js"
import 'vue-toastification/dist/index.css';

const app = createApp(App)

// Create a Pinia instance
const pinia = createPinia()

// Use the router and Pinia in your application
app.use(router)
app.use(pinia)
app.use(Toast, {
    position: POSITION.TOP_RIGHT,
    timeout: 2500,
    maxToasts: 3,
  });

// Mount the application
app.mount("#app")

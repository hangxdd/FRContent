import axios from "axios"

// Set the base URL for your Laravel backend
axios.defaults.baseURL = "http://localhost:8000"

// Enable withCredentials to send cookies with requests
axios.defaults.withCredentials = true

axios.defaults.withXSRFToken = true

// Set the X-Requested-With header to XMLHttpRequest
// This is a common practice to identify AJAX requests
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest"

// Configure the CSRF token settings
// Laravel uses XSRF-TOKEN cookie for CSRF tokens
axios.defaults.xsrfCookieName = "XSRF-TOKEN"
axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN"

// Export axios instance for use in your application
export default axios

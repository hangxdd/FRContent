import { createRouter, createWebHistory } from "vue-router"

const routes = [
  {
    path: "/",
    name: "Home",
    component: () => import("../pages/Home.vue"),
    meta: {
      title: "Home | FRContent",
    },
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("../pages/Login.vue"),
    meta: {
      title: "Login | FRContent",
    },
  },
  {
    path: "/forgotpass",
    name: "Forgot Password",
    component: () => import("../pages/ForgotPass.vue"),
    meta: {
      title: "Forgot Password | FRContent",
    },
  },
  {
    path: "/password-reset/:token",
    name: "Password Reset",
    component: () => import("../pages/ResetPass.vue"),
    meta: {
      title: "Password Reset | FRContent",
    },
  },
  {
    path: "/register",
    name: "Register",
    component: () => import("../pages/Register.vue"),
    meta: {
      title: "Register | FRContent",
    },
  },
  {
    path: "/scan",
    name: "Scan",
    component: () => import("../pages/Scan.vue"),
    meta: {
      title: "Scan | FRContent",
    },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from) => {
  document.title = to.meta?.title ?? "Default Title"
})

export default router

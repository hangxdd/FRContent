import { createRouter, createWebHistory } from "vue-router"

const routes = [
  {
    path: "/",
    name: "Home",
    component: () => import("../pages/Home.vue"),
    meta: {
      title: "Home | FRContent",
      requiresAuth: true,
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
    path: "/detect",
    name: "detect",
    component: () => import("../pages/Detect.vue"),
    meta: {
      title: "Detect | FRContent",
      requiresAuth: true,
    },
  },
  {
    path: "/faq",
    name: "FAQ",
    component: () => import("../pages/faq.vue"),
    meta: {
      title: "FAQ | FRContent",
      requiresAuth: true,
    },
  },
  {
    path: "/about",
    name: "About",
    component: () => import("../pages/About.vue"),
    meta: {
      title: "About | FRContent",
      requiresAuth: true,
    },
  },
  {
    path: "/randmovie",
    name: "Random Movie",
    component: () => import("../pages/randmovie.vue"),
    meta: {
      title: "Random | FRContent",
      requiresAuth: true,
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

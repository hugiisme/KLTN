import "./bootstrap";
import "../css/app.css";
import { createApp } from "vue";
import pinia from "./pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";
import Login from "./pages/Login/Login.vue";
import App from "./App.vue";
import router from "./router";

if (document.querySelector("#login-app")) {
    const app = createApp(Login);
    pinia.use(piniaPluginPersistedstate);

    app.use(pinia);
    app.mount("#login-app");
}

if (document.querySelector("#app")) {
    const app = createApp(App);
    pinia.use(piniaPluginPersistedstate);

    app.use(pinia);
    app.use(router);
    app.mount("#app");
}

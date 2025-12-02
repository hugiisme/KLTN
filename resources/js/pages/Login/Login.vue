<script setup>
import { ref } from "vue";
import LoginInput from "./LoginInput.vue";
import NotificationList from "@/components/Notifications/NotificationList.vue";
import Notification from "@/services/NotificationService.js";

const username = ref("");
const password = ref("");
const errors = ref({
    username: "",
    password: "",
});

const frontendValidate = () => {
    errors.value = { username: "", password: "" };

    let valid = true;
    if (username.value.trim() === "") {
        errors.value.username = "Tên đăng nhập không được để trống.";
        valid = false;
    }
    if (password.value.trim() === "") {
        errors.value.password = "Mật khẩu không được để trống.";
        valid = false;
    }
    return valid;
};

const submitLogin = async () => {
    if (!frontendValidate()) {
        return;
    }

    try {
        // Ensure CSRF cookie/token is set (useful after logout or cross-origin dev)
        try {
            await axios.get("/sanctum/csrf-cookie");
        } catch (e) {
            // ignore — some setups may not have sanctum, fallback to existing meta token
            console.warn("Could not fetch CSRF cookie:", e);
        }

        // Ensure axios will send the X-XSRF-TOKEN header by explicitly reading
        // the XSRF-TOKEN cookie and setting the header. This avoids cases where
        // the browser has the cookie but axios doesn't pick it up automatically
        // (cross-origin/dev setups).
        try {
            const getCookie = (name) => {
                const match = document.cookie.match(
                    new RegExp("(^|; )" + name + "=([^;]*)")
                );
                return match ? decodeURIComponent(match[2]) : null;
            };

            const xsrf = getCookie("XSRF-TOKEN");
            if (xsrf) {
                axios.defaults.headers.common["X-XSRF-TOKEN"] = xsrf;
            }
        } catch (e) {
            console.warn("Could not set XSRF header manually:", e);
        }

        const response = await axios.post("/api/login", {
            username: username.value,
            password: password.value,
        });

        // Lưu thông tin user vào sessionStorage để sử dụng phía client
        try {
            if (response.data && response.data.user) {
                sessionStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );
            } else {
                // Nếu backend không trả về user, gọi /api/me để lấy thông tin
                try {
                    const me = await axios.get("/api/me");
                    if (me.data && me.data.user) {
                        sessionStorage.setItem(
                            "user",
                            JSON.stringify(me.data.user)
                        );
                    }
                } catch (e) {
                    console.warn("Không lấy được /api/me:", e);
                }
            }
        } catch (e) {
            console.error("Lỗi khi lưu user vào sessionStorage:", e);
        }

        Notification.send("success", "Đăng nhập thành công!");
        window.location.href = "/";
    } catch (err) {
        // Nếu server trả về response
        let messageType = "";
        let message = "";
        if (err.response) {
            const status = err.response.status;

            if (status === 401) {
                // Authentication fail
                messageType = "error";
                message = "Tên đăng nhập hoặc mật khẩu không chính xác.";
            } else if (status === 403) {
                // Tài khoản bị khóa
                messageType = "error";
                message = "Tài khoản của bạn đã bị khóa.";
            }
            // else if (status === 422) {
            //     // Validation lỗi backend (trường hợp này ít xảy ra vì mình validate frontend)
            //     const dataErrors = err.response.data.errors || {};
            //     errors.value = { ...errors.value, ...dataErrors };
            // }
            else {
                messageType = "error";
                message = "Có lỗi xảy ra. Vui lòng thử lại.";
            }
        } else {
            // Không nhận được response (network error)
            messageType = "error";
            message = "Không thể kết nối đến máy chủ.";
        }
        Notification.send(messageType, message);
    }
};
</script>

<template>
    <form
        @submit.prevent="submitLogin"
        class="bg-white p-10 rounded-md shadow-2xl h-md w-md"
    >
        <h2 class="text-2xl font-bold mb-6 text-center">Đăng nhập</h2>

        <LoginInput
            label="Tên đăng nhập"
            type="text"
            v-model="username"
            :error="errors.username"
            icon="fa-solid fa-user"
        />

        <LoginInput
            label="Mật khẩu"
            type="password"
            v-model="password"
            :error="errors.password"
            icon="fa-solid fa-lock"
        />

        <button
            type="submit"
            class="bg-black text-white px-4 py-3 rounded-lg w-full border border-black hover:bg-white hover:text-black hover:scale-105 shadow-md hover:shadow-lg transition duration-200 ease-in-out font-semibold mt-3"
        >
            Đăng nhập
        </button>
    </form>
    <NotificationList />
</template>

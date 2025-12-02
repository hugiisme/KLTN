<script setup>
import { ref } from "vue";
import LoginInput from "./LoginInput.vue";
import NotificationList from "@/components/Notifications/NotificationList.vue";
import Notification from "@/services/NotificationService.js";
import axios from "axios";
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

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
        await axios.get("/sanctum/csrf-cookie");
        const response = await axios.post("/login", {
            username: username.value,
            password: password.value,
        });
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

<script setup>
import { ref, onMounted } from "vue";
import Notification from "../../services/NotificationService.js";
import LogoutService from "@/services/LogoutService.js";
const csrfToken = ref("");

onMounted(() => {
    csrfToken.value = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
});

const logout = LogoutService.logout;
</script>
<template>
    <div class="relative group py-3">
        <button
            class="px-4 py-2 text-gray-700 font-medium bg-white border border-gray-400 rounded-full hover:bg-gray-50 hover:shadow-sm transition-all duration-200 flex items-center gap-2"
        >
            <span>Tài khoản</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-gray-500"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>

        <div
            class="bg-white absolute right-0 mt-2 w-56 shadow-xl rounded-xl py-2 hidden group-hover:block border border-gray-200 z-50"
        >
            <a href="/me" class="block px-4 py-2 hover:bg-gray-100">
                Thông tin tài khoản
            </a>

            <button
                @click="logout"
                type="submit"
                class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 transition-colors"
            >
                Đăng xuất
            </button>
        </div>
    </div>
</template>

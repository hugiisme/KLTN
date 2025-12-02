<script setup>
import LogoutService from "@/services/LogoutService.js";
const props = defineProps({
    tabs: Array,
    activeTab: String,
});

const logout = LogoutService.logout;

const emit = defineEmits(["update:activeTab"]);
</script>

<template>
    <div class="w-70 bg-gray-50/50 border-r border-gray-200 flex flex-col">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-800">Cài đặt</h2>
            <p class="text-sm text-gray-500 mt-1">Quản lý tùy chọn của bạn</p>
        </div>

        <nav class="flex-1 px-4 space-y-1">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                @click="emit('update:activeTab', tab.id)"
                :class="[
                    'w-full flex items-center  gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200',
                    activeTab === tab.id
                        ? 'bg-blue-50 text-blue-700 shadow-sm'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                ]"
            >
                <i :class="['fa-solid', tab.icon]"></i>
                {{ tab.label }}
            </button>
        </nav>

        <div class="p-4 border-t border-gray-200">
            <button
                class="flex items-center gap-2 text-sm text-red-600 hover:text-red-700 font-medium px-4 py-2"
                @click="logout"
            >
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    org: { type: Object, required: true },
    userStatus: { type: String, default: "available" },
});

const emit = defineEmits(["register"]);

const statusConfig = computed(() => {
    switch (props.userStatus) {
        case "joined":
            return {
                label: "Đã tham gia",
                class: "bg-gray-100 text-gray-500 cursor-not-allowed",
                icon: "fa-solid fa-check",
            };
        case "pending":
            return {
                label: "Đang chờ duyệt",
                class: "bg-yellow-100 text-yellow-700 cursor-not-allowed",
                icon: "fa-solid fa-clock",
            };
        case "blocked_exclusive":
            return {
                label: "Đã có tổ chức",
                class: "bg-red-50 text-red-400 cursor-not-allowed",
                icon: "fa-solid fa-ban",
            };
        default:
            return {
                label: "Đăng ký tham gia",
                class: "bg-blue-600 text-white hover:bg-blue-700 shadow-md active:scale-95",
                icon: "fa-solid fa-user-plus",
            };
    }
});
</script>

<template>
    <div
        class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3 shadow-sm hover:shadow-md transition-all duration-200 h-full"
    >
        <div class="flex justify-between items-start">
            <div
                class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-xl"
            >
                <i class="fa-solid fa-users"></i>
            </div>
            <span
                class="text-xs font-semibold px-2 py-1 rounded bg-gray-100 text-gray-600 uppercase"
            >
                {{ org.org_type?.name || "Tổ chức" }}
            </span>
        </div>

        <div class="flex-1">
            <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1">
                {{ org.name || org.label }}
            </h3>
            <p class="text-sm text-gray-500 line-clamp-2">
                {{ org.description || "Chưa có mô tả cho tổ chức này." }}
            </p>
        </div>

        <div class="text-xs text-gray-400 flex items-center gap-1">
            <i class="fa-solid fa-layer-group"></i>
            {{ org.org_level?.equivalent_name || "Cấp chưa xác định" }}
        </div>

        <button
            class="w-full py-2.5 rounded-lg font-medium text-sm flex items-center justify-center gap-2 transition-all mt-auto"
            :class="statusConfig.class"
            :disabled="userStatus !== 'available'"
            @click="emit('register', org)"
        >
            <i :class="statusConfig.icon"></i>
            {{ statusConfig.label }}
        </button>
    </div>
</template>

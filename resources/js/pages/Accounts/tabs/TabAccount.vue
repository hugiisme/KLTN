<script setup>
import { ref, onMounted, computed } from "vue";

const account = ref({
    username: "",
    user_type: "",
    status: "",
    created_at: "",
    updated_at: "",
});

const formatDate = (dateString) => {
    if (!dateString) return "Chưa cập nhật";
    return new Date(dateString).toLocaleString("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const statusConfig = computed(() => {
    if (account.value.status === "active") {
        return {
            class: "bg-green-100 text-green-700 border-green-200",
            label: "Đang hoạt động",
            icon: "fa-check-circle",
        };
    } else if (account.value.status === "inactive") {
        return {
            class: "bg-gray-100 text-gray-600 border-gray-200",
            label: "Không hoạt động",
            icon: "fa-ban",
        };
    } else {
        return {
            class: "bg-yellow-100 text-yellow-700 border-yellow-200",
            label: account.value.status,
            icon: "fa-info-circle",
        };
    }
});

const loadUser = async () => {
    try {
        const res = await window.axios.get("/api/me");
        const data = res.data && res.data.user ? res.data.user : res.data;

        account.value = {
            username: data.username || "",
            user_type: data.user_type || "Chưa phân loại",
            status: data.status || "inactive",
            created_at: data.created_at || "",
            updated_at: data.updated_at || "",
        };
    } catch (e) {
        console.error("Load error", e);
    }
};

onMounted(loadUser);
</script>

<template>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-5 border-b border-gray-100 pb-8 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ account.username }}
                </h2>
                <div class="flex items-center gap-2 mt-1">
                    <span
                        class="text-sm text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded"
                    >
                        {{ account.user_type }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
            <div class="col-span-1 md:col-span-2">
                <label
                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2"
                >
                    Trạng thái tài khoản
                </label>
                <div
                    class="inline-flex items-center px-3 py-1.5 rounded-full border text-sm font-medium"
                    :class="statusConfig.class"
                >
                    <i class="fa-solid mr-2" :class="statusConfig.icon"></i>
                    {{ statusConfig.label }}
                </div>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                >
                    <i class="fa-solid fa-user-tag mr-1"></i> Tên đăng nhập
                </label>
                <p
                    class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2"
                >
                    {{ account.username }}
                </p>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                >
                    <i class="fa-solid fa-layer-group mr-1"></i> Loại người dùng
                </label>
                <p
                    class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2"
                >
                    {{ account.user_type }}
                </p>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                >
                    <i class="fa-regular fa-calendar-plus mr-1"></i> Ngày tham
                    gia
                </label>
                <p
                    class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2"
                >
                    {{ formatDate(account.created_at) }}
                </p>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1"
                >
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Cập nhật
                    lần cuối
                </label>
                <p
                    class="text-gray-900 font-medium text-base border-b border-gray-100 pb-2"
                >
                    {{ formatDate(account.updated_at) }}
                </p>
            </div>
        </div>
    </div>
</template>

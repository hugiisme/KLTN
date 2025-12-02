<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const account = ref({
    username: "",
    user_type: "",
    status: "",
    created_at: "",
    updated_at: "",
});

const loadUser = async () => {
    try {
        // Call the standard API endpoint for current user. Handle both
        // shapes: { user: { ... } } and direct user object.
        const res = await axios.get("/api/me");

        const data = res.data && res.data.user ? res.data.user : res.data;

        account.value = {
            username: data.username || "",
            user_type: data.user_type || data.userType || "",
            status: data.status || "",
            created_at: data.created_at || data.createdAt || "",
            updated_at: data.updated_at || data.updatedAt || "",
        };
    } catch (e) {
        console.error("Load error", e);
    }
};

onMounted(loadUser);
</script>

<template>
    <div class="space-y-6 p-4">
        <div class="space-y-2 text-gray-700 text-sm">
            <p><strong>Tên đăng nhập:</strong> {{ account.username }}</p>
            <p><strong>Mật khẩu:</strong> {{ account.password }}</p>
            <p><strong>Loại người dùng:</strong> {{ account.user_type }}</p>
            <p><strong>Trạng thái:</strong> {{ account.status }}</p>
            <p><strong>Ngày tạo:</strong> {{ account.created_at }}</p>
            <p><strong>Cập nhật lần cuối:</strong> {{ account.updated_at }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";

import Sidebar from "./components/Sidebar.vue";
import Header from "./components/Header.vue";

import AccountTabAccount from "./tabs/TabAccount.vue";
import AccountTabProfile from "./tabs/TabProfile.vue";
import AccountTabContact from "./tabs/TabContact.vue";
import AccountTabSecurity from "./tabs/TabSecurity.vue";
import AccountTabOrganization from "./tabs/TabOrganization.vue";
import AccountTabCertificates from "./tabs/TabCertificates.vue";

const tabs = [
    { id: "account", label: "Thông tin tài khoản", icon: "fa-user" },
    { id: "profile", label: "Hồ sơ cá nhân", icon: "fa-id-card" },
    { id: "contact", label: "Liên kết thông tin liên lạc", icon: "fa-link" },
    { id: "security", label: "Bảo mật & Đăng nhập", icon: "fa-shield-halved" },
    {
        id: "organization",
        label: "Tổ chức tôi tham gia",
        icon: "fa-building",
    },
    {
        id: "certificates",
        label: "Quản lý minh chứng cá nhân",
        icon: "fa-graduation-cap",
    },
];

const activeTab = ref("account");

const activeComponent = computed(() => {
    switch (activeTab.value) {
        case "account":
            return AccountTabAccount;
        case "profile":
            return AccountTabProfile;
        case "contact":
            return AccountTabContact;
        case "security":
            return AccountTabSecurity;
        case "organization":
            return AccountTabOrganization;
        case "certificates":
            return AccountTabCertificates;
    }
});
</script>

<template>
    <div class="bg-gray-50 flex items-center justify-center px-3">
        <div
            class="bg-white w-full h-[600px] flex rounded-2xl shadow-xl overflow-hidden border border-gray-300"
        >
            <Sidebar :tabs="tabs" v-model:activeTab="activeTab" />

            <div class="flex-1 flex flex-col overflow-hidden">
                <Header :title="tabs.find((t) => t.id === activeTab)?.label" />

                <main class="flex-1 overflow-y-auto p-8">
                    <component :is="activeComponent" class="animate-fade-in" />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.25s ease-out;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script setup>
import NotificationList from "@/components/Notifications/NotificationList.vue";
import NavBar from "@/components/Navigation/NavBar.vue";
</script>

<template>
    <NavBar />
    <div class="pt-16 h-full overflow-hidden">
        <router-view />
    </div>
    <NotificationList />
</template>

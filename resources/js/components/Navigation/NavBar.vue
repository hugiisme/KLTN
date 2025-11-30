<script setup>
import menu from "./nav_config.js";
import NavItem from "./NavItem.vue";
import UserMenu from "./UserMenu.vue";
</script>
<template>
    <nav
        class="fixed inset-x-0 top-0 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200 px-6 h-16 flex items-center justify-between z-20"
    >
        <div class="relative">
            <ul class="flex gap-4">
                <NavItem v-for="(item, i) in menu" :key="i" :item="item" />
            </ul>
        </div>
        <div class="relative">
            <UserMenu />
        </div>
    </nav>
</template>

<script setup>
import NavItem from "./NavItem.vue";
import { computed } from "vue";

const props = defineProps({
    item: Object,
    level: {
        type: Number,
        default: 1,
    },
});

const isActive = computed(() => {
    if (!props.item.url) return false;
    return window.location.pathname === props.item.url;
});
</script>

<template>
    <li class="relative nav-group">
        <!-- Item clickable -->
        <a
            v-if="item.url"
            :href="item.url"
            class="px-4 py-4 text-gray-700 hover:text-blue-600 whitespace-nowrap block rounded-lg transition-colors duration-200 hover:bg-gray-50"
            :class="[
                isActive
                    ? 'bg-blue-100 text-blue-700 font-bold'
                    : 'font-medium',
            ]"
        >
            {{ item.name }}
        </a>

        <span
            v-else
            class="cursor-pointer text-gray-700 hover:text-blue-600 whitespace-nowrap block rounded-lg transition-colors duration-200 hover:bg-gray-50 px-4 py-4 font-medium"
        >
            {{ item.name }}
        </span>

        <!-- BUFFER chống tụt hover -->
        <div v-if="item.children" class="hover-buffer"></div>

        <!-- Submenu -->
        <ul
            v-if="item.children"
            class="submenu absolute bg-white shadow-xl rounded-xl hidden min-w-[180px] border border-gray-100 py-1 z-10"
            :class="level === 1 ? 'top-full left-0 mt-1' : 'top-0 left-full'"
        >
            <NavItem
                v-for="(child, index) in item.children"
                :key="index"
                :item="child"
                :level="level + 1"
                class="block hover:bg-gray-100"
            />
        </ul>
    </li>
</template>

<style scoped>
.nav-group:hover > .submenu {
    display: block;
}

.hover-buffer {
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 12px;
    background: transparent;
    pointer-events: auto;
}
</style>

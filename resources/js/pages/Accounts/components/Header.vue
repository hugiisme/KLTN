<script setup>
const props = defineProps({
    title: {
        type: String,
        default: "Tài khoản của bạn",
    },
});
</script>

<template>
    <header
        class="w-full border-b bg-white px-6 py-4 flex items-center justify-between"
    >
        <!-- Left side: Title -->
        <h1 class="text-xl font-semibold text-gray-800">
            {{ title }}
        </h1>

        <!-- Right side: Optional actions -->
        <!-- Có thể đặt nút Edit, Save, Logout… sau này -->
        <slot name="actions"></slot>
    </header>
</template>

<style scoped></style>

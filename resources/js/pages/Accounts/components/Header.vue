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
        class="w-full border-b border-gray-500 bg-white px-6 py-4 flex items-center justify-between"
    >
        <h1 class="text-xl font-semibold text-gray-800">
            {{ title }}
        </h1>

        <slot name="actions"></slot>
    </header>
</template>

<style scoped></style>

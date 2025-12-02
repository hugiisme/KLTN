<script setup>
import { computed } from "vue";

const props = defineProps({
    currentPage: Number,
    totalPages: Number,
    maxPagesToShow: { type: Number, default: 5 },
});

const emit = defineEmits(["update:currentPage"]);

function go(p) {
    if (p < 1 || p > props.totalPages) return;
    emit("update:currentPage", p);
}

const pages = computed(() => {
    const arr = [];
    const { currentPage, totalPages, maxPagesToShow } = props;

    if (totalPages <= maxPagesToShow) {
        for (let i = 1; i <= totalPages; i++) arr.push(i);
    } else {
        let start = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let end = start + maxPagesToShow - 1;

        if (end > totalPages) {
            end = totalPages;
            start = end - maxPagesToShow + 1;
        }

        for (let i = start; i <= end; i++) arr.push(i);
    }

    return arr;
});
</script>

<template>
    <div class="flex gap-2 justify-center items-center mt-2">
        <button @click="go(currentPage - 1)" :disabled="currentPage === 1">
            Prev
        </button>

        <button v-if="pages[0] > 1" @click="go(1)">1</button>
        <span v-if="pages[0] > 2">…</span>

        <button
            v-for="p in pages"
            :key="p"
            @click="go(p)"
            :class="{
                'font-bold bg-blue-500 text-black border-2 border-black rounded px-2':
                    p === currentPage,
            }"
        >
            {{ p }}
        </button>

        <span v-if="pages[pages.length - 1] < totalPages - 1">…</span>
        <button
            v-if="pages[pages.length - 1] < totalPages"
            @click="go(totalPages)"
        >
            {{ totalPages }}
        </button>

        <button
            @click="go(currentPage + 1)"
            :disabled="currentPage === totalPages"
        >
            Next
        </button>
    </div>
</template>

<style scoped>
button {
    padding: 4px 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: white;
    cursor: pointer;
}
button:disabled {
    opacity: 0.5;
}
</style>

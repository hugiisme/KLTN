<script setup>
import { computed } from "vue";

const props = defineProps({
    currentPage: { type: Number, default: 1 },
    totalPages: { type: Number, required: true },
    maxPagesToShow: { type: Number, default: 5 }, // số trang hiển thị
});

const emit = defineEmits(["update:currentPage"]);

function goTo(page) {
    if (page < 1 || page > props.totalPages) return;
    emit("update:currentPage", page);
}

// Tính dãy trang cần hiển thị
const pages = computed(() => {
    const pagesArray = [];
    const { currentPage, totalPages, maxPagesToShow } = props;

    if (totalPages <= maxPagesToShow) {
        for (let i = 1; i <= totalPages; i++) pagesArray.push(i);
    } else {
        let start = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let end = start + maxPagesToShow - 1;

        if (end > totalPages) {
            end = totalPages;
            start = end - maxPagesToShow + 1;
        }

        for (let i = start; i <= end; i++) pagesArray.push(i);
    }
    return pagesArray;
});
</script>

<template>
    <div class="flex gap-2 justify-center items-center mt-2">
        <button @click="goTo(currentPage - 1)" :disabled="currentPage === 1">
            Prev
        </button>

        <button v-if="pages[0] > 1" @click="goTo(1)">1</button>
        <span v-if="pages[0] > 2">…</span>

        <button
            v-for="page in pages"
            :key="page"
            @click="goTo(page)"
            :class="{
                'font-bold bg-blue-500 text-black border-2 border-black rounded px-2':
                    page === currentPage,
            }"
        >
            {{ page }}
        </button>

        <span v-if="pages[pages.length - 1] < totalPages - 1">…</span>
        <button
            v-if="pages[pages.length - 1] < totalPages"
            @click="goTo(totalPages)"
        >
            {{ totalPages }}
        </button>

        <button
            @click="goTo(currentPage + 1)"
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
    cursor: not-allowed;
}
</style>

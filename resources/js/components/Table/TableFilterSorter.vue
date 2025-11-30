<script setup>
import { ref } from "vue";

const props = defineProps({
    filters: Array, // e.g. [{name:'type', label:'Type'}]
    sorters: Array, // e.g. [{name:'label', label:'Label'}]
});

const emit = defineEmits(["search", "filter", "sort"]);

const searchText = ref("");
const selectedFilter = ref("");
const selectedSorter = ref("");

function onSearch() {
    emit("search", searchText.value);
}

function onFilterChange() {
    emit("filter", selectedFilter.value);
}

function onSortChange() {
    emit("sort", selectedSorter.value);
}
</script>

<template>
    <div
        class="flex gap-3 mb-2 items-center bg-gray-50 p-3 rounded-lg border border-gray-100"
    >
        <div class="relative flex-1">
            <input
                type="text"
                v-model="searchText"
                placeholder="Tìm kiếm..."
                @input="onSearch"
                class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all bg-white"
            />
            <span class="absolute left-3 top-2.5 text-gray-400 text-xs">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>

        <select
            v-if="filters?.length"
            v-model="selectedFilter"
            @change="onFilterChange"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white cursor-pointer hover:bg-gray-50 transition-colors"
        >
            <option value="">Tất cả phân loại</option>
            <option v-for="f in filters" :key="f.name" :value="f.name">
                {{ f.label }}
            </option>
        </select>

        <select
            v-if="sorters?.length"
            v-model="selectedSorter"
            @change="onSortChange"
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 bg-white cursor-pointer hover:bg-gray-50 transition-colors"
        >
            <option value="">Sắp xếp mặc định</option>
            <option v-for="s in sorters" :key="s.name" :value="s.name">
                {{ s.label }}
            </option>
        </select>
    </div>
</template>

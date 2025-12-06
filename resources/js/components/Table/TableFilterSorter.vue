<script setup>
import { ref } from "vue";

const props = defineProps({
    filters: Array,
    sorters: Array,
});

const emit = defineEmits(["search", "filter", "sort"]);

const searchText = ref("");
const selectedFilter = ref("");

const selectedSorter = ref("");
const selectedSortDirection = ref("asc");

// Search chính là filter value
function onSearch() {
    emit("search", searchText.value);
}

// Chỉ chọn field, không nhập value
function onFilterChange() {
    emit("filter", selectedFilter.value);
}

function onSortChange() {
    const field = selectedSorter.value;
    const direction = selectedSortDirection.value;
    emit(
        "sort",
        field ? { field, direction } : { field: "", direction: "asc" }
    );
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
                class="w-full border border-gray-400 rounded-lg pl-9 pr-3 py-2 text-sm"
            />
            <span class="absolute left-3 top-2.5 text-gray-400 text-xs">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>

        <select
            v-if="filters?.length"
            v-model="selectedFilter"
            @change="onFilterChange"
            class="border border-gray-400 rounded-lg px-3 py-2 text-sm"
        >
            <option value="">Lọc theo...</option>
            <option v-for="f in filters" :key="f.name" :value="f.name">
                {{ f.label }}
            </option>
        </select>

        <div v-if="sorters?.length" class="flex gap-2">
            <select
                v-model="selectedSorter"
                @change="onSortChange"
                class="border border-gray-400 rounded-lg px-3 py-2 text-sm"
            >
                <option value="">Sắp xếp theo...</option>
                <option v-for="s in sorters" :key="s.name" :value="s.name">
                    {{ s.label }}
                </option>
            </select>

            <select
                v-model="selectedSortDirection"
                @change="onSortChange"
                class="border border-gray-400 rounded-lg px-3 py-2 text-sm"
            >
                <option value="asc">Tăng dần</option>
                <option value="desc">Giảm dần</option>
            </select>
        </div>
    </div>
</template>

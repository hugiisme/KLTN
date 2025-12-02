<script setup>
import { ref, watch } from "vue";

// Props
const props = defineProps({
    fields: {
        type: Array,
        default: () => [{ name: "label", label: "Tên" }],
    },
    types: {
        type: Array,
        default: () => [], // expected: [{ label: "Năm học", value: "academic_year" }]
    },
});

// Emits
const emit = defineEmits(["search", "sort", "filter"]);

// State
const searchText = ref("");
const selectedSortField = ref(props.fields[0]?.name ?? "label");
const sortDirection = ref("asc");
const selectedType = ref("all");

// Debounce search
let searchTimer = null;
watch(searchText, (newVal) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        emit("search", { text: newVal });
    }, 250);
});

// Watch sort
watch(
    [selectedSortField, sortDirection],
    ([field, dir], [oldField, oldDir]) => {
        if (field === oldField && dir === oldDir) return; // tránh emit lần đầu
        emit("sort", { field, direction: dir });
    }
);

// Watch filter
watch(selectedType, (val, oldVal) => {
    if (val === oldVal) return;
    emit("filter", { type: val });
});
</script>

<template>
    <div
        class="tree-filter flex flex-col gap-3 p-4 border-b border-gray-100 bg-white"
    >
        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase"
                >Tìm kiếm</label
            >
            <div class="relative">
                <input
                    type="text"
                    v-model="searchText"
                    placeholder="Nhập tên..."
                    class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
                />
                <span class="absolute right-3 top-2.5 text-gray-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase"
                >Sắp xếp</label
            >
            <div class="flex gap-2">
                <select
                    v-model="selectedSortField"
                    class="flex-1 border border-gray-400 rounded-lg px-2 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
                >
                    <option
                        v-for="field in fields"
                        :key="field.name"
                        :value="field.name"
                    >
                        {{ field.label }}
                    </option>
                </select>

                <select
                    v-model="sortDirection"
                    class="w-28 border border-gray-400 rounded-lg px-2 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
                >
                    <option value="asc">Tăng dần</option>
                    <option value="desc">Giảm dần</option>
                </select>
            </div>
        </div>

        <div v-if="types.length > 0" class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-gray-500 uppercase"
                >Lọc</label
            >
            <select
                v-model="selectedType"
                class="w-full border border-gray-400 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
            >
                <option value="all">Tất cả</option>
                <option v-for="t in types" :key="t.value" :value="t.value">
                    {{ t.label }}
                </option>
            </select>
        </div>
    </div>
</template>

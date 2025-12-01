<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";

import TableFilterSorter from "@/components/Table/TableFilterSorter.vue";
import DataTable from "@/components/Table/DataTable.vue";
import Pagination from "@/components/Table/Pagination.vue";

const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, default: "fa-solid fa-list" },

    /** Object được chọn (hiện label ở góc phải) */
    selected: { type: Object, default: null },

    /** API để load bảng */
    apiUrl: { type: String, required: true },

    /** Hàm sinh params từ selected */
    apiParams: {
        type: Function,
        default: (selected) => ({}),
    },

    /** cấu hình lọc/sắp xếp */
    filters: { type: Array, default: () => [] },
    sorters: { type: Array, default: () => [] },

    /** cấu hình bảng */
    columns: { type: Array, required: true },

    /** rows custom (nếu muốn override API) */
    customRows: { type: Array, default: null },

    pageSize: { type: Number, default: 10 },
});

const emit = defineEmits(["search", "filter", "sort", "edit", "delete"]);

// STATE
const data = ref([]);
const currentPage = ref(1);

// FETCH API
async function reload() {
    if (!props.selected) return;

    const params = props.apiParams(props.selected);

    const res = await axios.get(props.apiUrl, { params });
    data.value = res.data;
}
defineExpose({ reload });

// LOAD AGAIN WHEN selected CHANGED
watch(
    () => props.selected,
    () => {
        currentPage.value = 1;
        reload();
    },
    { immediate: true }
);

// PAGINATION
const totalPages = computed(() =>
    Math.max(
        1,
        Math.ceil((props.customRows ?? data.value).length / props.pageSize)
    )
);

const paginatedRows = computed(() => {
    const rows = props.customRows ?? data.value;
    const start = (currentPage.value - 1) * props.pageSize;
    return rows.slice(start, start + props.pageSize);
});
</script>

<template>
    <div
        class="flex-1 bg-white border border-gray-200 rounded-xl shadow-sm p-5 flex flex-col gap-4 h-full overflow-hidden"
    >
        <!-- HEADER -->
        <div
            class="flex items-center justify-between border-b border-gray-100 pb-3"
        >
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <span class="text-blue-600">
                    <i :class="icon"></i>
                </span>
                <span>{{ title }}</span>
            </h3>

            <!-- label trái phải -->
            <span
                v-if="selected"
                class="text-sm px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-medium"
            >
                {{ selected.label }}
            </span>
            <span
                v-else
                class="text-sm px-3 py-1 bg-gray-100 text-gray-500 rounded-full font-medium"
            >
                Chưa chọn mục
            </span>
        </div>

        <!-- FILTER + SORT -->
        <TableFilterSorter
            :filters="filters"
            :sorters="sorters"
            @search="(txt) => emit('search', txt)"
            @filter="(f) => emit('filter', f)"
            @sort="(s) => emit('sort', s)"
        />

        <!-- TABLE -->
        <DataTable
            v-if="selected"
            :columns="columns"
            :rows="paginatedRows"
            :page-size="pageSize"
            @edit="(row) => emit('edit', row)"
            @delete="(row) => emit('delete', row)"
        />

        <p v-else class="text-gray-500 italic">
            Vui lòng chọn một mục để xem danh sách.
        </p>

        <!-- PAGINATION -->
        <Pagination
            v-if="selected"
            v-model:currentPage="currentPage"
            :totalPages="totalPages"
        />
    </div>
</template>

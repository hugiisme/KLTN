<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";

import TableFilterSorter from "@/components/Table/TableFilterSorter.vue";
import DataTable from "@/components/Table/DataTable.vue";
import Pagination from "@/components/Table/Pagination.vue";

const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, default: "fa-solid fa-list" },
    selected: { type: Object, default: null },
    apiUrl: { type: String, required: true },
    apiParams: { type: Function, default: () => ({}) },

    filters: { type: Array, default: () => [] },
    sorters: { type: Array, default: () => [] },

    columns: { type: Array, required: true },

    customRows: { type: Array, default: null },
    pageSize: { type: Number, default: 10 },
});

const emit = defineEmits(["edit", "delete"]);

// DATA
const rows = ref([]);
const meta = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    from: null,
    to: null,
    per_page: props.pageSize,
});

const currentPage = ref(1);
const searchText = ref("");
const selectedFilter = ref("");
const sorter = ref({ field: "id", direction: "asc" }); // Default sort by id
const tableSort = ref({ field: null, direction: null });

async function reload() {
    if (!props.selected && props.customRows === null) {
        rows.value = [];
        meta.value = {
            current_page: 1,
            last_page: 1,
            per_page: props.pageSize,
            total: 0,
        };
        return;
    }

    if (props.customRows !== null) {
        rows.value = props.customRows;
        meta.value = {
            current_page: 1,
            last_page: 1,
            per_page: props.customRows.length,
            total: props.customRows.length,
            from: 1,
            to: props.customRows.length,
        };
        return;
    }

    const base = props.apiParams(props.selected) || {};

    const params = {
        ...base,
        page: currentPage.value,
        per_page: props.pageSize,

        search: searchText.value || undefined,

        filter_field: selectedFilter.value || undefined,
        filter_value: selectedFilter.value ? searchText.value : undefined,

        sort_field: tableSort.value.field || sorter.value.field || undefined,
        sort_direction:
            tableSort.value.direction || sorter.value.direction || "asc",
    };

    const res = await axios.get(props.apiUrl, { params });

    rows.value = res.data.data;

    meta.value = {
        current_page: res.data.current_page,
        last_page: res.data.last_page,
        per_page: res.data.per_page,
        total: res.data.total,
        from: res.data.from,
        to: res.data.to,
    };
}

defineExpose({ reload });

// WATCH
watch(
    () => props.selected,
    () => {
        currentPage.value = 1;
        reload();
    },
    { immediate: true }
);

watch(currentPage, reload);

watch(
    () => props.customRows,
    () => {
        if (props.customRows) {
            rows.value = [...props.customRows];
            meta.value = {
                current_page: 1,
                last_page: 1,
                per_page: props.customRows.length,
                total: props.customRows.length,
                from: 1,
                to: props.customRows.length,
            };
        }
    },
    { deep: true }
);

// EVENTS
function onSearch(v) {
    searchText.value = v.trim();
    currentPage.value = 1;
    reload();
}

function onFilter(fieldName) {
    selectedFilter.value = fieldName || "";
    currentPage.value = 1;
    reload();
}

function onSorter(obj) {
    sorter.value = obj || { field: "", direction: "asc" };
    currentPage.value = 1;
    reload();
}

function onTableSort(info) {
    tableSort.value = info || { field: null, direction: null };
    currentPage.value = 1;
    reload();
}

const totalPages = computed(() => meta.value.last_page || 1);
</script>

<template>
    <div
        class="flex-1 bg-white border border-gray-300 rounded-xl shadow-sm p-5 flex flex-col gap-4 h-full overflow-hidden"
    >
        <div
            class="flex items-center justify-between border-b border-gray-500 pb-3"
        >
            <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                <span class="text-blue-600"><i :class="icon"></i></span>
                <span>{{ title }}</span>
            </h3>

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

        <TableFilterSorter
            :filters="filters"
            :sorters="sorters"
            @search="onSearch"
            @filter="onFilter"
            @sort="onSorter"
        />

        <DataTable
            v-if="selected || customRows"
            :columns="columns"
            :rows="rows"
            @edit="emit('edit', $event)"
            @delete="emit('delete', $event)"
            @sort="onTableSort"
        >
            <template
                v-for="(slot, name) in $slots"
                :key="name"
                v-slot:[name]="slotProps"
            >
                <slot :name="name" v-bind="slotProps" />
            </template>
        </DataTable>

        <p v-else class="text-gray-500 italic">
            Vui lòng chọn một mục để xem danh sách.
        </p>

        <Pagination
            v-if="(selected || customRows) && totalPages > 1"
            v-model:currentPage="currentPage"
            :totalPages="totalPages"
        />
    </div>
</template>

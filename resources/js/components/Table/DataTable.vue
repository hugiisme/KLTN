<script setup>
import { ref } from "vue";

const props = defineProps({
    columns: Array, // [{ key:'name', label:'Tên', sortable:true }]
    rows: Array,
});

const emit = defineEmits(["sort"]);

const sortState = ref({
    field: null,
    direction: null, // asc | desc | null
});

function getValue(obj, key) {
    if (!obj || key == null) return "";
    if (key.indexOf(".") === -1) return obj[key] ?? "";
    const parts = key.split(".");
    let cur = obj;
    for (const p of parts) {
        if (cur == null) return "";
        cur = cur[p];
    }
    if (cur == null) return "";
    if (typeof cur === "object") return JSON.stringify(cur);
    return cur;
}

function toggleSort(col) {
    if (!col.sortable) return;

    if (sortState.value.field !== col.key) {
        sortState.value = { field: col.key, direction: "asc" };
    } else {
        if (sortState.value.direction === "asc") {
            sortState.value.direction = "desc";
        } else if (sortState.value.direction === "desc") {
            sortState.value.direction = null;
        } else {
            sortState.value.direction = "asc";
        }
    }
    emit("sort", { ...sortState.value });
}

function getSortIcon(col) {
    if (sortState.value.field !== col.key) return "↕";
    if (sortState.value.direction === "asc") return "▲";
    if (sortState.value.direction === "desc") return "▼";
    return "↕";
}
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-300 shadow-sm">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-6 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wide select-none cursor-pointer hover:bg-gray-200 transition-colors"
                        @click="toggleSort(col)"
                    >
                        <div class="flex items-center gap-2">
                            {{ col.label }}
                            <span
                                v-if="col.sortable"
                                class="text-gray-500 text-xs"
                            >
                                {{ getSortIcon(col) }}
                            </span>
                        </div>
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-300">
                <tr
                    v-for="row in rows"
                    :key="row.id"
                    class="hover:bg-blue-50 transition-colors duration-150"
                >
                    <td
                        v-for="col in columns"
                        :key="col.key"
                        class="px-6 py-4 whitespace-nowrap text-base text-gray-800 font-medium"
                    >
                        <span
                            :class="
                                col.type === 'date'
                                    ? 'font-mono text-sm text-gray-900 font-bold'
                                    : ''
                            "
                        >
                            {{ getValue(row, col.key) }}
                        </span>
                    </td>
                </tr>

                <tr v-if="rows.length === 0">
                    <td
                        :colspan="columns.length"
                        class="px-6 py-10 text-center text-gray-500 text-base italic"
                    >
                        Không có dữ liệu hiển thị
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

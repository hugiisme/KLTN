<script setup>
import { ref } from "vue";

const props = defineProps({
    columns: Array, // [{ key:'created_at', label:'Ngày tạo', type: 'date', sortable:true }]
    rows: Array,
});

const emit = defineEmits(["sort", "edit", "delete"]);

const sortState = ref({
    field: null,
    direction: null, // asc | desc | null
});

function formatDate(value) {
    if (!value) return "";
    const date = new Date(value);
    // Kiểm tra nếu date không hợp lệ thì trả về giá trị gốc
    if (isNaN(date.getTime())) return value;

    // Format theo kiểu Việt Nam: dd/mm/yyyy
    return new Intl.DateTimeFormat("vi-VN", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        // Bỏ comment dòng dưới nếu muốn hiện cả giờ phút
        // hour: "2-digit", minute: "2-digit"
    }).format(date);
}
// ---------------------------------------

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
                        <template v-if="$slots[`cell-${col.key}`]">
                            <slot :name="`cell-${col.key}`" :row="row" />
                        </template>

                        <div
                            v-else-if="col.type === 'actions'"
                            class="flex gap-2"
                        >
                            <button
                                class="px-3 py-1 rounded bg-yellow-200 text-yellow-800 text-sm hover:bg-yellow-300"
                                @click.stop="emit('edit', row)"
                            >
                                Sửa
                            </button>
                            <button
                                class="px-3 py-1 rounded bg-red-200 text-red-800 text-sm hover:bg-red-300"
                                @click.stop="emit('delete', row)"
                            >
                                Xóa
                            </button>
                        </div>

                        <span
                            v-else
                            :class="
                                col.type === 'date'
                                    ? 'font-mono text-sm text-gray-900 font-bold'
                                    : ''
                            "
                        >
                            {{
                                col.type === "date"
                                    ? formatDate(getValue(row, col.key))
                                    : getValue(row, col.key)
                            }}
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

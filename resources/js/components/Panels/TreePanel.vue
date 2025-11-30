<script setup>
import TreeView from "../TreeView/TreeView.vue";
import TreeFilter from "../TreeView/TreeFilter.vue";
import { ref } from "vue";

const props = defineProps({
    treeLabel: String,
    treeData: Array,
    selected: Object,
    filterTypes: Array,
    sortFields: Array,
});

const emit = defineEmits([
    "search",
    "sort",
    "filter",
    "select",
    "toggle-expand",
]);

const treeView = ref(null);
defineExpose({ treeView });
</script>

<template>
    <div
        class="h-full w-80 bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col overflow-hidden"
    >
        <div class="p-3 border-b border-gray-100 bg-gray-50">
            <h2
                class="font-bold text-gray-700 text-center uppercase text-sm tracking-wide"
            >
                {{ treeLabel }}
            </h2>
        </div>

        <TreeFilter
            :fields="sortFields"
            :types="filterTypes"
            @search="emit('search', $event)"
            @sort="emit('sort', $event)"
            @filter="emit('filter', $event)"
        />

        <TreeView
            ref="treeView"
            class="flex-1 min-h-0 overflow-y-auto"
            :data="props.treeData"
            :selected="props.selected ?? null"
            @select="emit('select', $event)"
            @toggle-expand="emit('toggle-expand', $event)"
        />
    </div>
</template>

<script setup>
import TreePanel from "@/components/Panels/TreePanel.vue";

const props = defineProps({
    treeData: Array,
    selected: Object,

    treeLabel: { type: String, default: "Danh sách" },
    sortFields: { type: Array, default: () => [] },
    filterTypes: { type: Array, default: () => [] },
});

const emit = defineEmits(["search", "sort", "filter", "select"]);
</script>

<template>
    <TreePanel
        :treeLabel="treeLabel"
        :treeData="treeData"
        :selected="selected"
        :sortFields="sortFields"
        :filterTypes="filterTypes"
        @select="emit('select', $event)"
        @search="emit('search', $event)"
        @sort="emit('sort', $event)"
        @filter="emit('filter', $event)"
    />
</template>

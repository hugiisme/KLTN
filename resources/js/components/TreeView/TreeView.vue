<script setup>
import { ref, watch, defineExpose } from "vue";
import TreeNode from "./TreeNode.vue";

const props = defineProps({
    data: { type: Array, required: true },
    selected: { type: Object, default: null },
});

const emit = defineEmits(["select", "toggle-expand"]);

const expandedState = ref(new Map());

watch(
    () => props.data,
    () => {
        expandedState.value = new Map();
    }
);

// Helpers
function isExpanded(id) {
    return expandedState.value.get(id) === true;
}

function toggleExpand(id) {
    const map = new Map(expandedState.value);
    map.set(id, !map.get(id));
    expandedState.value = map;
}

defineExpose({
    expandNode(id) {
        const map = new Map(expandedState.value);
        map.set(id, true);
        expandedState.value = map;
    },
    collapseNode(id) {
        const map = new Map(expandedState.value);
        map.set(id, false);
        expandedState.value = map;
    },
    getExpandedState() {
        return expandedState.value;
    },
});

function onSelect(node) {
    emit("select", node);
}

function onToggleExpand(id) {
    toggleExpand(id);
    emit("toggle-expand", id);
}
</script>

<template>
    <div class="tree-view" role="tree">
        <template v-for="node in props.data" :key="node.type + '-' + node.id">
            <TreeNode
                :node="node"
                :level="0"
                :selected="props.selected"
                :isExpanded="isExpanded(node.id)"
                :isNodeExpanded="isExpanded"
                @select="onSelect"
                @toggle-expand="onToggleExpand"
            />
        </template>
    </div>
</template>

<style scoped>
.tree-view {
    width: 100%;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 8px;
    scrollbar-width: thin;
    scrollbar-color: #9ca3af #f3f4f6;
    box-sizing: border-box;
}

.tree-view::-webkit-scrollbar {
    width: 6px;
}

.tree-view::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 3px;
}

.tree-view::-webkit-scrollbar-thumb {
    background: #9ca3af;
    border-radius: 3px;
}

.tree-view::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>

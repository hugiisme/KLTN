<script setup>
import { ref, watch, defineExpose } from "vue";
import TreeNode from "./TreeNode.vue";

const props = defineProps({
    data: { type: Array, required: true },
    selected: { type: Object, default: null },
});

const emit = defineEmits(["select", "toggle-expand"]);

// expanded state map (id -> boolean)
const expandedState = ref(new Map());

// Reset expand state khi data thay đổi hoàn toàn
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

// Expose API cho parent (AcademicYears.vue)
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

// emit wrapper
function onSelect(node) {
    emit("select", node);
}

function onToggleExpand(id) {
    toggleExpand(id);
    emit("toggle-expand", id);
}
</script>

<template>
    <div class="tree-view overflow-y-auto" role="tree">
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
    padding: 12px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
</style>

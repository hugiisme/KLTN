<script setup>
import { computed } from "vue";

const props = defineProps({
    node: { type: Object, required: true },
    level: { type: Number, default: 0 },
    selected: { type: Object, default: null },
    isExpanded: { type: Boolean, default: false },
    isNodeExpanded: { type: Function, required: true }, // nhận từ TreeView
});

const emit = defineEmits(["select", "toggle-expand"]);

const hasChildren = computed(
    () => Array.isArray(props.node.children) && props.node.children.length > 0
);

function onToggleExpand(e) {
    e.stopPropagation();
    emit("toggle-expand", props.node.id);
}

function onSelect(e) {
    e.stopPropagation();
    emit("select", props.node);
}
</script>

<template>
    <div class="tree-node">
        <div
            class="node-row flex items-center gap-2 py-1.5 px-2 rounded-lg cursor-pointer transition-colors duration-150 border border-transparent hover:bg-gray-100"
            :class="{
                'node-selected bg-blue-100 border-blue-300 text-blue-800 font-semibold shadow-sm':
                    selected &&
                    selected.id === node.id &&
                    selected.type === node.type,
            }"
            :style="{ paddingLeft: `${level * 12}px` }"
            @click="onSelect"
            tabindex="0"
            role="treeitem"
            :aria-expanded="hasChildren ? isExpanded : undefined"
        >
            <button
                v-if="hasChildren"
                class="caret w-5 h-5 flex items-center justify-center rounded hover:bg-black/5"
                @click="onToggleExpand"
            >
                <i
                    class="fa-solid transition-transform duration-200"
                    :class="[
                        isExpanded ? 'fa-caret-down' : 'fa-caret-right',
                        selected && selected.id === node.id
                            ? 'text-blue-600'
                            : 'text-gray-400',
                    ]"
                ></i>
            </button>

            <span v-else class="w-5"></span>

            <span class="text-sm truncate select-none">{{ node.label }}</span>
        </div>

        <div v-if="hasChildren && isExpanded" class="children" role="group">
            <TreeNode
                v-for="child in node.children"
                :key="child.type + '-' + child.id"
                :node="child"
                :level="level + 1"
                :selected="selected"
                :isExpanded="isNodeExpanded(child.id)"
                :isNodeExpanded="isNodeExpanded"
                @select="emit('select', $event)"
                @toggle-expand="emit('toggle-expand', $event)"
            />
        </div>
    </div>
</template>

<style scoped>
.node-row:focus-visible {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}

.children {
    margin-left: 0;
    margin-top: 2px;
    border-left: 1px dashed #d1d5db;
}
</style>

import { ref, computed } from "vue";
import Notification from "@/services/NotificationService.js";
export default function useOrgTree(treeData) {
    const treeToRender = ref([]);
    const currentSearch = ref("");
    const currentSort = ref({ field: "label", direction: "asc" });
    const currentFilter = ref("all");

    function normalizeNode(node, parent = null) {
        if (!node) return null;
        return {
            ...node,
            label: node.label ?? node.name ?? "",
            parent: parent ? { id: parent.id, label: parent.name } : null,
            children: Array.isArray(node.children)
                ? node.children
                      .map((c) => normalizeNode(c, node))
                      .filter(Boolean)
                : [],
        };
    }

    function filterTreeByType(tree, filterType) {
        const filterByType = (nodes) =>
            nodes
                .map((node) => {
                    if (!node || !node.label) return null;
                    const match = node.type === filterType;
                    const children = node.children
                        ? filterByType(node.children)
                        : [];
                    if (match || children.length) return { ...node, children };
                    return null;
                })
                .filter(Boolean);
        return filterByType(tree);
    }

    function searchInTree(tree, text) {
        const searchTree = (nodes) =>
            nodes
                .map((node) => {
                    if (!node || !node.label) return null;
                    const label = node.label ?? "";
                    const match = label
                        .toLowerCase()
                        .includes(text.toLowerCase());
                    const children = node.children
                        ? searchTree(node.children)
                        : [];
                    if (match || children.length) return { ...node, children };
                    return null;
                })
                .filter(Boolean);
        return searchTree(tree);
    }

    function sortTreeByField(tree, field, direction) {
        const sortTree = (nodes) =>
            [...nodes]
                .filter((n) => n && n.label)
                .sort((a, b) => {
                    const av = (a[field] ?? "").toString();
                    const bv = (b[field] ?? "").toString();
                    return direction === "asc"
                        ? av.localeCompare(bv)
                        : bv.localeCompare(av);
                })
                .map((node) => {
                    if (node.children && node.children.length) {
                        node.children = sortTree(node.children);
                    } else {
                        node.children = [];
                    }
                    return node;
                });
        return sortTree(tree);
    }

    const orgList = computed(() =>
        treeData.value
            .filter((n) => n && n.type === "organization" && n.label)
            .map((n) => ({ id: n.id, label: n.label ?? n.name }))
    );

    function updateTreeToRender() {
        if (!treeData.value || !treeData.value.length) {
            treeToRender.value = [];
            return;
        }

        let temp = JSON.parse(JSON.stringify(treeData.value));

        if (currentFilter.value !== "all") {
            temp = filterTreeByType(temp, currentFilter.value);
        }

        if (currentSearch.value) {
            temp = searchInTree(temp, currentSearch.value);
        }

        temp = sortTreeByField(
            temp,
            currentSort.value.field,
            currentSort.value.direction
        );

        treeToRender.value = temp;
    }

    function onTreeSearch({ text }) {
        currentSearch.value = text;
        updateTreeToRender();
    }

    function onTreeSort({ field, direction }) {
        currentSort.value = { field, direction };
        updateTreeToRender();
    }

    function onTreeFilter({ type }) {
        currentFilter.value = type;
        updateTreeToRender();
    }

    return {
        treeToRender,
        orgList,
        updateTreeToRender,
        onTreeSearch,
        onTreeSort,
        onTreeFilter,
        normalizeNode,
    };
}

import { ref, computed } from "vue";
import Notification from "@/services/NotificationService.js";

export default function useAcademicTree(treeData) {
    const treeToRender = ref([]);
    const currentSearch = ref("");
    const currentSort = ref({ field: "label", direction: "asc" });
    const currentFilter = ref("all");

    function normalizeNode(node) {
        if (!node) return null;
        return {
            ...node,
            label: node.label ?? node.name ?? "",
            children: Array.isArray(node.children)
                ? node.children.map(normalizeNode).filter(Boolean)
                : [],
        };
    }

    function filterTreeByType(nodes, filterType) {
        const filterByType = (items) =>
            items
                .map((node) => {
                    const match = node.type === filterType;
                    const children = node.children
                        ? filterByType(node.children)
                        : [];
                    if (match || children.length) return { ...node, children };
                })
                .filter(Boolean);
        return filterByType(nodes);
    }

    function searchInTree(nodes, text) {
        const searchTree = (items) =>
            items
                .map((node) => {
                    const match = node.label
                        .toLowerCase()
                        .includes(text.toLowerCase());
                    const children = node.children
                        ? searchTree(node.children)
                        : [];
                    if (match || children.length) return { ...node, children };
                })
                .filter(Boolean);
        return searchTree(nodes);
    }

    function sortTreeByField(nodes, field, direction) {
        const sortTree = (items) =>
            [...items]
                .sort((a, b) => {
                    const av = a[field] ?? "";
                    const bv = b[field] ?? "";
                    if (av < bv) return direction === "asc" ? -1 : 1;
                    if (av > bv) return direction === "asc" ? 1 : -1;
                    return 0;
                })
                .map((node) => {
                    if (node.children) {
                        node.children = sortTree(node.children);
                    }
                    return node;
                });
        return sortTree(nodes);
    }

    const academicYears = computed(() => {
        const data = Array.isArray(treeData.value) ? treeData.value : [];
        return data
            .filter((n) => n.type === "academic_year")
            .map((n) => ({ id: n.id, label: n.label }));
    });

    function updateTreeToRender() {
        try {
            let temp = Array.isArray(treeData.value)
                ? treeData.value.map(normalizeNode)
                : [];

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
        } catch (err) {
            console.error("Error in updateTreeToRender:", err);
            Notification.send("error", "Lỗi khi render tree");
            treeToRender.value = [];
        }
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
        academicYears,
        updateTreeToRender,
        onTreeSearch,
        onTreeSort,
        onTreeFilter,
    };
}

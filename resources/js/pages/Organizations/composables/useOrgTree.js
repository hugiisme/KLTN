import { ref, computed } from "vue";
import Notification from "@/services/NotificationService.js";

export default function useOrgTree(treeData) {
    const treeToRender = ref([]);
    const currentSearch = ref("");
    const currentSort = ref({ field: "label", direction: "asc" });
    const currentFilter = ref("all");

    function updateTreeToRender() {
        if (!treeData.value || !treeData.value.length) {
            treeToRender.value = [];
            return;
        }

        let temp = JSON.parse(JSON.stringify(treeData.value));

        // Filter
        if (currentFilter.value !== "all") {
            const filterByType = (tree) =>
                tree
                    .map((node) => {
                        if (!node || !node.label) return null;
                        const match = node.type === currentFilter.value;
                        const children = node.children
                            ? filterByType(node.children)
                            : [];
                        if (match || children.length)
                            return { ...node, children };
                        return null;
                    })
                    .filter(Boolean);
            temp = filterByType(temp);
        }

        // Search
        if (currentSearch.value) {
            const searchTree = (tree, text) =>
                tree
                    .map((node) => {
                        if (!node || !node.label) return null;
                        const label = node.label ?? "";
                        const match = label
                            .toLowerCase()
                            .includes(text.toLowerCase());
                        const children = node.children
                            ? searchTree(node.children, text)
                            : [];
                        if (match || children.length)
                            return { ...node, children };
                        return null;
                    })
                    .filter(Boolean);
            temp = searchTree(temp, currentSearch.value);
        }

        // Sort
        const sortTree = (tree, field, direction) =>
            [...tree]
                .filter((n) => n && n.label) // filter node rỗng
                .sort((a, b) => {
                    const av = (a[field] ?? "").toString();
                    const bv = (b[field] ?? "").toString();
                    return direction === "asc"
                        ? av.localeCompare(bv)
                        : bv.localeCompare(av);
                })
                .map((node) => {
                    if (node.children && node.children.length) {
                        node.children = sortTree(
                            node.children,
                            field,
                            direction
                        );
                    } else {
                        node.children = [];
                    }
                    return node;
                });

        temp = sortTree(
            temp,
            currentSort.value.field,
            currentSort.value.direction
        );
        treeToRender.value = temp;
    }

    const orgList = computed(() =>
        treeData.value
            .filter((n) => n && n.type === "organization" && n.label)
            .map((n) => ({ id: n.id, label: n.label ?? n.name }))
    );

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
    };
}

import { ref, computed } from "vue";
import Notification from "@/services/NotificationService.js";

export default function useAcademicTree(treeData) {
    const treeToRender = ref([]);
    const currentSearch = ref("");
    const currentSort = ref({ field: "label", direction: "asc" });
    const currentFilter = ref("all");

    // Hàm chuẩn hóa node: luôn có label và children là mảng
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

    // Update tree để render
    function updateTreeToRender() {
        try {
            let temp = Array.isArray(treeData.value)
                ? treeData.value.map(normalizeNode)
                : [];
            // Filter theo type
            if (currentFilter.value !== "all") {
                const filterByType = (nodes) =>
                    nodes
                        .map((node) => {
                            const match = node.type === currentFilter.value;
                            const children = node.children
                                ? filterByType(node.children)
                                : [];
                            if (match || children.length)
                                return { ...node, children };
                        })
                        .filter(Boolean);
                temp = filterByType(temp);
            }

            // Search theo label
            if (currentSearch.value) {
                const searchTree = (nodes, text) =>
                    nodes
                        .map((node) => {
                            const match = node.label
                                .toLowerCase()
                                .includes(text.toLowerCase());
                            const children = node.children
                                ? searchTree(node.children, text)
                                : [];
                            if (match || children.length)
                                return { ...node, children };
                        })
                        .filter(Boolean);
                temp = searchTree(temp, currentSearch.value);
            }

            // Sort
            const sortTree = (nodes, field, direction) =>
                [...nodes]
                    .sort((a, b) => {
                        const av = a[field] ?? "";
                        const bv = b[field] ?? "";
                        if (av < bv) return direction === "asc" ? -1 : 1;
                        if (av > bv) return direction === "asc" ? 1 : -1;
                        return 0;
                    })
                    .map((node) => {
                        if (node.children) {
                            node.children = sortTree(
                                node.children,
                                field,
                                direction
                            );
                        }
                        return node;
                    });

            temp = sortTree(
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

    // Computed list of academic years
    const academicYears = computed(() => {
        const data = Array.isArray(treeData.value) ? treeData.value : [];
        return data
            .filter((n) => n.type === "academic_year")
            .map((n) => ({ id: n.id, label: n.label }));
    });

    // Event handlers
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

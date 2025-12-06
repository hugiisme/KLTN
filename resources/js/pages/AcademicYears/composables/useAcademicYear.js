import { ref } from "vue";
import Notification from "@/services/NotificationService.js";
import AcademicYearService from "@/services/AcademicYearService.js";

export default function useAcademicYear(
    selectedNode,
    treeData,
    reloadTree,
    semesterPanelRef
) {
    const selectedYear = ref(null);
    const isYearModalOpen = ref(false);
    const modalMode = ref("create");
    const modalInitialData = ref(null);

    function formatDate(date) {
        return date?.toString().slice(0, 10) || "";
    }

    function remapSelectedYear() {
        if (!selectedYear.value) return;

        const newNode = treeData.value
            .flatMap((year) => [year, ...(year.semesters ?? [])])
            .find((n) => n.id === selectedYear.value.id);

        selectedYear.value = newNode || null;
    }

    function openCreateYearModal() {
        modalMode.value = "create";
        modalInitialData.value = null;
        isYearModalOpen.value = true;
    }

    function openEditYearModal() {
        if (!selectedYear.value) return;
        modalMode.value = "edit";
        modalInitialData.value = {
            name: selectedYear.value.label,
            start_date: formatDate(selectedYear.value.start_date),
            end_date: formatDate(selectedYear.value.end_date),
        };
        isYearModalOpen.value = true;
    }

    async function handleYearSubmit(data) {
        try {
            if (modalMode.value === "create") {
                await AcademicYearService.create(data);
                Notification.send("success", "Tạo năm học thành công");
            } else {
                await AcademicYearService.update(selectedYear.value.id, data);
                Notification.send("success", "Cập nhật năm học thành công");
            }
            isYearModalOpen.value = false;
            await reloadTree();
            remapSelectedYear();
            semesterPanelRef.value.reload();
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi lưu năm học");
        }
    }

    function onSelectYear(node) {
        selectedNode.value = { id: node.id, type: node.type };
        if (node.type === "academic_year") selectedYear.value = node;
        else if (node.type === "semester")
            selectedYear.value = node.academic_year;
    }

    async function deleteYear() {
        if (!selectedYear.value?.id) return;

        if (!confirm("Bạn có chắc muốn xoá năm học này?")) return;

        try {
            await AcademicYearService.delete(selectedYear.value.id);
            Notification.send("success", "Đã xoá năm học");

            isYearModalOpen.value = false;
            await reloadTree();
            selectedYear.value = null;
            semesterPanelRef.value.reload();
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi xoá năm học");
        }
    }

    return {
        selectedYear,
        isYearModalOpen,
        modalMode,
        modalInitialData,
        openCreateYearModal,
        openEditYearModal,
        handleYearSubmit,
        onSelectYear,
        deleteYear,
    };
}

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

    const formatDate = (date) => date?.toString().slice(0, 10) || "";

    const openCreateYearModal = () => {
        modalMode.value = "create";
        modalInitialData.value = null;
        isYearModalOpen.value = true;
    };

    const openEditYearModal = () => {
        if (!selectedYear.value) return;
        modalMode.value = "edit";
        modalInitialData.value = {
            name: selectedYear.value.label,
            start_date: formatDate(selectedYear.value.start_date),
            end_date: formatDate(selectedYear.value.end_date),
        };
        isYearModalOpen.value = true;
    };

    const handleYearSubmit = async (data) => {
        try {
            if (modalMode.value === "create") {
                await AcademicYearService.createYear(data);
                Notification.send("success", "Tạo năm học thành công");
            } else {
                await AcademicYearService.updateYear(
                    selectedYear.value.id,
                    data
                );
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
    };

    const onSelectYear = (node) => {
        selectedNode.value = { id: node.id, type: node.type };
        if (node.type === "academic_year") selectedYear.value = node;
        else if (node.type === "semester")
            selectedYear.value = node.academic_year;
    };

    const deleteYear = async () => {
        if (!selectedYear.value?.id) return;

        if (!confirm("Bạn có chắc muốn xoá năm học này?")) return;

        try {
            await AcademicYearService.deleteYear(selectedYear.value.id);
            Notification.send("success", "Đã xoá năm học");

            isYearModalOpen.value = false;
            await reloadTree();
            selectedYear.value = null;
            semesterPanelRef.value.reload();
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi xoá năm học");
        }
    };

    function remapSelectedYear() {
        if (!selectedYear.value) return;

        const newNode = treeData.value
            .flatMap((year) => [year, ...(year.semesters ?? [])])
            .find((n) => n.id === selectedYear.value.id);

        selectedYear.value = newNode || null;
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

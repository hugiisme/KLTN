import { ref } from "vue";
import Notification from "@/services/NotificationService.js";
import AcademicYearService from "@/services/AcademicYearService.js";

export default function useAcademicYear(selectedNode, treeData, reloadTree) {
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

    return {
        selectedYear,
        isYearModalOpen,
        modalMode,
        modalInitialData,
        openCreateYearModal,
        openEditYearModal,
        handleYearSubmit,
        onSelectYear,
    };
}

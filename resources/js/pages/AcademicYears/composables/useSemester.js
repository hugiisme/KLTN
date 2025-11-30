import { ref } from "vue";
import Notification from "@/services/NotificationService.js";
import SemesterService from "@/services/SemesterService.js";

export default function useSemester(
    selectedYear,
    treeData,
    reloadTree,
    treePanelRef,
    semesterPanelRef
) {
    const isSemesterModalOpen = ref(false);
    const semesterModalMode = ref("create");
    const semesterModalInitialData = ref({});

    const openCreateSemesterModal = () => {
        semesterModalMode.value = "create";
        semesterModalInitialData.value = {
            academic_year_id: selectedYear.value?.id || null,
            name: "",
            start_date: "",
            end_date: "",
        };
        isSemesterModalOpen.value = true;
    };

    const handleSemesterSubmit = async (data) => {
        if (!data.academic_year_id) {
            Notification.send("error", "Năm học không được để trống");
            return;
        }

        const year = treeData.value.find((y) => y.id === data.academic_year_id);
        if (!year) {
            Notification.send("error", "Không tìm thấy năm học");
            return;
        }

        // Validation ngày
        const yearStart = new Date(year.start_date);
        const yearEnd = new Date(year.end_date);
        const semStart = new Date(data.start_date);
        const semEnd = new Date(data.end_date);

        if (isNaN(semStart) || isNaN(semEnd)) {
            Notification.send("error", "Ngày không hợp lệ");
            return;
        }
        if (semStart < yearStart || semEnd > yearEnd) {
            Notification.send("error", "Ngày học kỳ không hợp lệ với năm học");
            return;
        }
        if (semEnd < semStart) {
            Notification.send(
                "error",
                "Ngày kết thúc học kỳ không được sớm hơn ngày bắt đầu"
            );
            return;
        }

        try {
            if (semesterModalMode.value === "create") {
                await SemesterService.create(data);
                Notification.send("success", "Tạo học kỳ thành công");
            } else {
                await SemesterService.update(data.id, data);
                Notification.send("success", "Cập nhật học kỳ thành công");
            }

            isSemesterModalOpen.value = false;
            await reloadTree();

            if (semesterPanelRef.value?.reload) {
                semesterPanelRef.value.reload(); // gọi hàm reload table
            }

            // Expand node trên tree
            if (treePanelRef.value?.treeView) {
                treePanelRef.value.treeView.expandNode(data.academic_year_id);
            }
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi lưu học kỳ");
        }
    };

    return {
        isSemesterModalOpen,
        semesterModalMode,
        semesterModalInitialData,
        openCreateSemesterModal,
        handleSemesterSubmit,
    };
}

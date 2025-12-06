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

    function formatDate(date) {
        return date?.toString().slice(0, 10) || "";
    }

    function validateSemesterDates(data, year) {
        const yearStart = new Date(year.start_date);
        const yearEnd = new Date(year.end_date);
        const semStart = new Date(data.start_date);
        const semEnd = new Date(data.end_date);

        if (isNaN(semStart) || isNaN(semEnd)) {
            Notification.send("error", "Ngày không hợp lệ");
            return false;
        }

        if (semStart < yearStart || semEnd > yearEnd) {
            Notification.send("error", "Ngày học kỳ không hợp lệ với năm học");
            return false;
        }

        if (semEnd < semStart) {
            Notification.send(
                "error",
                "Ngày kết thúc học kỳ không được sớm hơn ngày bắt đầu"
            );
            return false;
        }

        return true;
    }

    async function refreshAfterChange(academicYearId) {
        await reloadTree();

        if (semesterPanelRef.value?.reload) {
            semesterPanelRef.value.reload();
        }

        if (treePanelRef.value?.treeView) {
            treePanelRef.value.treeView.expandNode(academicYearId);
        }
    }

    function openCreateSemesterModal() {
        semesterModalMode.value = "create";
        semesterModalInitialData.value = {
            academic_year_id: selectedYear.value?.id || null,
            name: "",
            start_date: "",
            end_date: "",
        };
        isSemesterModalOpen.value = true;
    }

    function openEditSemester(row) {
        if (!row) return;

        semesterModalMode.value = "edit";
        semesterModalInitialData.value = {
            id: row.id,
            academic_year_id: row.academic_year_id,
            name: row.name,
            start_date: formatDate(row.start_date),
            end_date: formatDate(row.end_date),
        };

        isSemesterModalOpen.value = true;
    }

    async function handleSemesterSubmit(data) {
        // Validation
        if (!data.academic_year_id) {
            Notification.send("error", "Năm học không được để trống");
            return;
        }

        const year = treeData.value.find((y) => y.id === data.academic_year_id);
        if (!year) {
            Notification.send("error", "Không tìm thấy năm học");
            return;
        }

        if (!validateSemesterDates(data, year)) {
            return;
        }

        try {
            if (semesterModalMode.value === "create") {
                await SemesterService.create(data);
                Notification.send("success", "Tạo học kỳ thành công");
            } else {
                if (!data.id) {
                    Notification.send("error", "ID học kỳ không xác định");
                    return;
                }

                await SemesterService.update(data.id, {
                    academic_year_id: data.academic_year_id,
                    name: data.name,
                    start_date: data.start_date,
                    end_date: data.end_date,
                });
                Notification.send("success", "Cập nhật học kỳ thành công");
            }

            isSemesterModalOpen.value = false;
            await refreshAfterChange(data.academic_year_id);
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi lưu học kỳ");
        }
    }

    async function deleteSemester(row) {
        if (!row?.id) return;
        if (!confirm("Bạn có chắc muốn xoá học kỳ này?")) return;

        try {
            await SemesterService.delete(row.id);
            Notification.send("success", "Đã xoá học kỳ");

            await refreshAfterChange(row.academic_year_id);
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi xoá học kỳ");
        }
    }

    return {
        isSemesterModalOpen,
        semesterModalMode,
        semesterModalInitialData,
        openCreateSemesterModal,
        handleSemesterSubmit,
        openEditSemester,
        deleteSemester,
    };
}

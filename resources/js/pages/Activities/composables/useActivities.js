import { ref, watch } from "vue";
import ActivityService from "@/services/ActivityService.js";
import Notification from "@/services/NotificationService.js";

function toDateInput(value) {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";
    return date.toISOString().slice(0, 10); // yyyy-mm-dd for input[type=date]
}

function toBoolean(value) {
    if (value === true || value === false) return value;
    if (value === 1 || value === "1") return true;
    if (value === 0 || value === "0") return false;
    return Boolean(value);
}

export default function useActivities(selectedOrg, rightPanelRef) {
    const isModalOpen = ref(false);
    const modalMode = ref("create");
    const modalInitialData = ref({});

    // Khi chọn Org bên trái -> Reload bảng bên phải
    // Logic này thực ra đã được RightPanel handle qua prop :selected
    // Nhưng ta có thể watch thêm nếu cần reset state gì đó

    function openCreateModal() {
        if (!selectedOrg.value) {
            Notification.send(
                "warning",
                "Vui lòng chọn một tổ chức trước khi thêm hoạt động"
            );
            return;
        }
        modalMode.value = "create";
        modalInitialData.value = { org_id: selectedOrg.value.id }; // Auto fill Org hiện tại
        isModalOpen.value = true;
    }

    function openEditModal(row) {
        modalMode.value = "edit";
        modalInitialData.value = {
            ...row,
            start_time: toDateInput(row.start_time),
            end_time: toDateInput(row.end_time),
            is_visible: toBoolean(row.is_visible ?? true),
        };
        isModalOpen.value = true;
    }

    async function handleActivitySubmit(formData) {
        try {
            if (modalMode.value === "create") {
                await ActivityService.create(formData);
                Notification.send("success", "Thêm hoạt động thành công");
            } else {
                // await ActivityService.update(...)
            }
            isModalOpen.value = false;
            rightPanelRef.value.reload(); // Reload table
        } catch (err) {
            Notification.send("error", "Có lỗi xảy ra");
        }
    }

    return {
        isModalOpen,
        modalMode,
        modalInitialData,
        openCreateModal,
        openEditModal,
        handleActivitySubmit,
    };
}

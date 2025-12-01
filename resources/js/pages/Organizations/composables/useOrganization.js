import { ref } from "vue";
import Notification from "@/services/NotificationService.js";
import OrganizationService from "@/services/OrganizationService.js";

export default function useOrganization(
    selectedNode,
    treeData,
    loadTree,
    parentOrgs,
    loadDropdowns,
    treePanelRef,
    isOrgModalOpen,
    modalMode,
    modalInitialData,
    rightPanelRef
) {
    const selectedOrg = ref(null);

    // Open modal: create
    async function openCreateOrgModal() {
        if (!parentOrgs.value.length) await loadDropdowns();

        const initial = {};
        if (selectedNode.value) initial.parent_org_id = selectedNode.value.id;

        modalInitialData.value = initial;
        modalMode.value = "create";
        isOrgModalOpen.value = true;
    }

    // Open modal: edit
    async function openEditOrgModal(node) {
        if (!node) return;

        if (!parentOrgs.value.length) await loadDropdowns();

        try {
            const detailedNode = await OrganizationService.getById(node.id);

            modalInitialData.value = {
                id: detailedNode.id,
                name: detailedNode.name ?? "",
                parent_org_id: detailedNode.parent?.id ?? null,
                org_type_id: detailedNode.org_type_id ?? null,
                org_level_id: detailedNode.org_level_id ?? null,
                ...detailedNode,
            };

            modalMode.value = "edit";
            isOrgModalOpen.value = true;
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi load dữ liệu tổ chức");
        }
    }

    // Remap selectedNode sau khi reload tree
    function remapSelectedOrg() {
        if (!selectedNode.value) return;

        const newNode = treeData.value
            .flatMap((n) => [n, ...(n.children ?? [])])
            .find((n) => n.id === selectedNode.value.id);

        selectedNode.value = newNode || null;
    }

    // Submit form
    async function handleOrgSubmit(formData) {
        if (!formData || typeof formData !== "object") return;

        try {
            if (modalMode.value === "create") {
                await OrganizationService.create(formData);
                Notification.send("success", "Lưu tổ chức thành công");
            } else if (modalMode.value === "edit") {
                if (!formData.id) {
                    Notification.send(
                        "error",
                        "Không có ID tổ chức để cập nhật"
                    );
                    return;
                }
                await OrganizationService.update(formData.id, formData);
                Notification.send("success", "Cập nhật tổ chức thành công");
            }

            isOrgModalOpen.value = false;

            // Reload tree và RightPanel
            await loadTree();
            remapSelectedOrg();
            if (rightPanelRef?.value?.reload) rightPanelRef.value.reload();
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi lưu tổ chức");
        }
    }

    // Select node
    function onSelectOrg(node) {
        selectedNode.value = node;
    }

    // Delete node
    async function deleteOrg(row) {
        if (!row?.id) return;
        if (!confirm("Bạn có chắc muốn xoá tổ chức này?")) return;

        try {
            await OrganizationService.delete(row.id);
            Notification.send("success", "Đã xoá tổ chức");

            // Reload tree và RightPanel
            await loadTree();
            remapSelectedOrg();
            if (rightPanelRef?.value?.reload) rightPanelRef.value.reload();

            // Expand parent node
            if (treePanelRef.value?.treeView && row.parent?.id) {
                treePanelRef.value.treeView.expandNode(row.parent.id);
            }

            // Đóng modal nếu đang mở đúng tổ chức bị xoá
            if (isOrgModalOpen.value && modalInitialData.value?.id === row.id) {
                isOrgModalOpen.value = false;
            }
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi xoá tổ chức");
        }
    }

    return {
        selectedOrg,
        isOrgModalOpen,
        modalMode,
        modalInitialData,
        openCreateOrgModal,
        openEditOrgModal,
        handleOrgSubmit,
        onSelectOrg,
        deleteOrg,
    };
}

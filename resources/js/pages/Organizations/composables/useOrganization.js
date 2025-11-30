import { ref } from "vue";
import Notification from "@/services/NotificationService.js";
import OrganizationService from "@/services/OrganizationService.js";

export default function useOrganization(selectedNode, treeData, reloadTree) {
    const selectedOrg = ref(null);
    const isOrgModalOpen = ref(false);
    const modalMode = ref("create");
    const modalInitialData = ref({});

    // --------------------
    // Open modals
    // --------------------
    const openCreateOrgModal = async () => {
        modalMode.value = "create";
        modalInitialData.value = {
            name: "",
            parent_org_id: null,
            all_orgs: treeData.value,
            org_types: await OrganizationService.getTypes(),
            org_levels: await OrganizationService.getLevels(),
        };
        isOrgModalOpen.value = true;
    };

    const openEditOrgModal = async () => {
        if (!selectedOrg.value) return;
        modalMode.value = "edit";

        modalInitialData.value = {
            ...selectedOrg.value,
            all_orgs: treeData.value.filter(
                (x) => x.id !== selectedOrg.value.id
            ),
            org_types: await OrganizationService.getTypes(),
            org_levels: await OrganizationService.getLevels(),
        };

        isOrgModalOpen.value = true;
    };

    // --------------------
    // Submit
    // --------------------
    const handleOrgSubmit = async (data) => {
        try {
            if (modalMode.value === "create") {
                await OrganizationService.create(data);
                Notification.send("success", "Tạo tổ chức thành công");
            } else {
                await OrganizationService.update(selectedOrg.value.id, data);
                Notification.send("success", "Cập nhật tổ chức thành công");
            }

            isOrgModalOpen.value = false;
            await reloadTree();
        } catch (err) {
            console.error(err);
            Notification.send("error", "Lỗi khi lưu tổ chức");
        }
    };

    // --------------------
    // Select on tree
    // --------------------
    const onSelectOrg = (node) => {
        selectedNode.value = node;
        selectedOrg.value = node;
    };

    return {
        selectedOrg,
        isOrgModalOpen,
        modalMode,
        modalInitialData,
        openCreateOrgModal,
        openEditOrgModal,
        handleOrgSubmit,
        onSelectOrg,
    };
}

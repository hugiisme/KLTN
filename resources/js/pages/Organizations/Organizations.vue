<script setup>
import { ref, onMounted } from "vue";

import LeftPanel from "@/components/Panels/LeftPanel.vue";
import RightPanel from "@/components/Panels/RightPanel.vue";
import UpperPanel from "@/components/Panels/UpperPanel.vue";
import OrgModal from "./modals/OrgModal.vue";

import useOrgTree from "./composables/useOrgTree";
import useOrganization from "./composables/useOrganization";
import Notification from "@/services/NotificationService.js";

import OrganizationService from "@/services/OrganizationService.js";
import OrgTypeService from "@/services/OrgTypeService.js";
import OrgLevelService from "@/services/OrgLevelService.js";

// Tree và selection
const treeData = ref([]);
const selectedNode = ref(null);
const treePanelRef = ref(null);

// Dropdown data
const orgTypes = ref([]);
const orgLevels = ref([]);
const parentOrgs = ref([]);

// Modal control
const isOrgModalOpen = ref(false);
const modalMode = ref("create");
const modalInitialData = ref(null);

// Load dropdown data
async function loadDropdowns() {
    try {
        const [types, levels, parents] = await Promise.all([
            OrgTypeService.getAll(),
            OrgLevelService.getAll(),
            OrganizationService.getTree(),
        ]);

        orgTypes.value = types;
        orgLevels.value = levels;
        parentOrgs.value = parents;
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi khi load dữ liệu tổ chức");
    }
}

// Chuẩn hóa node
function normalizeNode(node, parent = null) {
    if (!node) return null;
    return {
        ...node,
        label: node.label ?? node.name ?? "",
        parent: parent
            ? { id: parent.id, label: parent.name ?? parent.label ?? "" }
            : null,
        children: Array.isArray(node.children)
            ? node.children.map((c) => normalizeNode(c, node)).filter(Boolean)
            : [],
    };
}

// Load tree
async function loadTree() {
    try {
        const res = await OrganizationService.getTree();
        treeData.value = res.map(normalizeNode);
        updateTreeToRender();
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi khi load tree tổ chức");
    }
}

// Composables
const {
    treeToRender,
    updateTreeToRender,
    onTreeSearch,
    onTreeSort,
    onTreeFilter,
} = useOrgTree(treeData);

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
        // Lấy dữ liệu chi tiết từ backend
        const detailedNode = await OrganizationService.getById(node.id);
        console.log("detailedNode", detailedNode);

        if (!detailedNode || typeof detailedNode !== "object") {
            Notification.send("error", "Dữ liệu tổ chức không hợp lệ");
            return;
        }

        if (!detailedNode.id) {
            Notification.send("error", "Dữ liệu tổ chức thiếu ID");
            return;
        }

        // Chuẩn hóa dữ liệu modal
        modalInitialData.value = {
            id: detailedNode.id, // đảm bảo luôn có ID
            name: detailedNode.name ?? "",
            parent_org_id: detailedNode.parent?.id ?? null,
            org_type_id: detailedNode.org_type_id ?? null,
            org_level_id: detailedNode.org_level_id ?? null,
            // Thêm các trường khác nếu cần
            ...detailedNode,
        };

        modalMode.value = "edit";
        isOrgModalOpen.value = true;
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi khi load dữ liệu tổ chức");
    }
}

// Submit form
function handleOrgSubmit(formData) {
    if (!formData || typeof formData !== "object") return;

    if (modalMode.value === "create") {
        OrganizationService.create(formData)
            .then(() => {
                Notification.send("success", "Lưu tổ chức thành công");
                isOrgModalOpen.value = false;
                loadTree();
            })
            .catch((err) => {
                console.error(err);
                Notification.send("error", "Lỗi khi lưu tổ chức");
            });
    } else if (modalMode.value === "edit") {
        if (!formData.id) {
            Notification.send("error", "Không có ID tổ chức để cập nhật");
            return;
        }

        OrganizationService.update(formData.id, formData)
            .then(() => {
                Notification.send("success", "Cập nhật tổ chức thành công");
                isOrgModalOpen.value = false;
                loadTree();
            })
            .catch((err) => {
                console.error(err);
                Notification.send("error", "Lỗi khi cập nhật tổ chức");
            });
    }
}

// Select
function onSelectOrg(node) {
    selectedNode.value = node;
}

onMounted(async () => {
    await loadDropdowns();
    await loadTree();
});

// Handle action từ UpperPanel
function handleUpperAction(event) {
    if (event === "create-org") openCreateOrgModal();
    if (event === "edit-org") openEditOrgModal(selectedNode.value);
}
</script>

<template>
    <div class="h-full flex flex-col gap-4 p-2">
        <UpperPanel
            title="Quản lý tổ chức"
            icon="fa-solid fa-building"
            :buttons="[
                {
                    label: 'Thêm tổ chức mới',
                    icon: 'fa-solid fa-plus',
                    event: 'create-org',
                    class: 'flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: true,
                },
                {
                    label: 'Sửa thông tin',
                    icon: 'fa-solid fa-pen-to-square',
                    event: 'edit-org',
                    class: 'flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: () => !!selectedNode,
                },
            ]"
            @action="handleUpperAction"
        />

        <div class="flex h-full gap-4">
            <LeftPanel
                ref="treePanelRef"
                :treeData="treeToRender"
                :selected="selectedNode"
                :sortFields="[
                    { name: 'name', label: 'Tên tổ chức' },
                    { name: 'org_type_id', label: 'Loại hình' },
                    { name: 'org_level_id', label: 'Cấp độ' },
                ]"
                :filterTypes="[
                    { label: 'Theo loại', value: 'org_type' },
                    { label: 'Theo cấp độ', value: 'org_level' },
                ]"
                :org-types="orgTypes"
                :org-levels="orgLevels"
                @select="onSelectOrg"
                @search="onTreeSearch"
                @sort="onTreeSort"
                @filter="onTreeFilter"
            />

            <RightPanel
                title="Danh sách tổ chức con"
                icon="fa-solid fa-list"
                :selected="selectedNode"
                api-url=""
                :custom-rows="selectedNode?.children ?? []"
                :columns="[
                    { key: 'label', label: 'Tên tổ chức' },
                    { key: 'org_type.name', label: 'Loại' },
                    { key: 'org_level.equivalent_name', label: 'Cấp' },
                ]"
                :filters="[{ name: 'label', label: 'Tên' }]"
                :sorters="[{ name: 'label', label: 'Tên' }]"
            />
        </div>
    </div>

    <OrgModal
        v-model="isOrgModalOpen"
        :mode="modalMode"
        :initialData="modalInitialData"
        :org-types="orgTypes"
        :org-levels="orgLevels"
        :parent-organizations="parentOrgs"
        @submit="handleOrgSubmit"
    />
</template>

<script setup>
import { ref, onMounted } from "vue";

import LeftPanel from "@/components/Panels/LeftPanel.vue";
import RightPanel from "@/components/Panels/RightPanel.vue";
import UpperPanel from "@/components/Panels/UpperPanel.vue";
import UserModal from "./modals/UserModal.vue";

import OrganizationService from "@/services/OrganizationService.js";
import UserService from "@/services/UserService.js";
import Notification from "@/services/NotificationService.js";

import useOrgTree from "@/pages/Organizations/composables/useOrgTree.js";

const treeData = ref([]);
const selectedNode = ref(null);

const {
    treeToRender,
    updateTreeToRender,
    onTreeSearch,
    onTreeSort,
    onTreeFilter,
} = useOrgTree(treeData);

const users = ref([]);

const isUserModalOpen = ref(false);
const modalMode = ref("create");
const modalInitialData = ref(null);

function normalizeNode(node, parent = null) {
    return {
        ...node,
        label: node.label ?? node.name ?? "(Không tên)",
        parent: parent
            ? { id: parent.id, label: parent.label ?? parent.name }
            : null,
        children: node.children?.map((c) => normalizeNode(c, node)) ?? [],
    };
}

async function loadTree() {
    try {
        const res = await OrganizationService.getTree();
        treeData.value = res.map((n) => normalizeNode(n));
        updateTreeToRender();
    } catch (e) {
        console.error(e);
        Notification.send("error", "Không load được cây tổ chức");
    }
}

async function loadUsersByOrg(orgId) {
    if (!orgId) return (users.value = []);
    try {
        const res = await UserService.getByOrg(orgId);
        users.value = Array.isArray(res) ? res : [];
    } catch (e) {
        console.error(e);
        Notification.send("error", "Không thể tải người dùng");
    }
}

function onSelectOrg(node) {
    selectedNode.value = node;
    loadUsersByOrg(node.id);
}

function handleAction(action) {
    if (action === "create-user") {
        if (!selectedNode.value) {
            return Notification.send(
                "warning",
                "Chọn tổ chức trước khi thêm user"
            );
        }
        modalMode.value = "create";
        modalInitialData.value = { org_id: selectedNode.value.id };
        isUserModalOpen.value = true;
    }

    if (action === "import-user") {
        Notification.send("info", "Tính năng import đang phát triển...");
    }
}

async function handleSubmit(data) {
    try {
        if (modalMode.value === "create") {
            await UserService.create(data);
            Notification.send("success", "Đã thêm người dùng mới");
        } else {
            await UserService.update(data.id, data);
            Notification.send("success", "Đã cập nhật người dùng");
        }
        isUserModalOpen.value = false;

        const orgId = selectedNode.value.id;
        const res = await UserService.getByOrg(orgId);
        users.value = Array.isArray(res) ? [...res] : [];
    } catch (e) {
        Notification.send("error", "Lỗi khi lưu người dùng");
        console.error(e);
    }
}

async function deleteUser(id) {
    if (!confirm("Xóa người dùng này?")) return;
    try {
        await UserService.delete(id);
        Notification.send("success", "Đã xóa");
        loadUsersByOrg(selectedNode.value.id);
    } catch (e) {
        console.error(e);
        Notification.send("error", "Lỗi khi xóa người dùng");
    }
}

function openEditUserModal(row) {
    modalMode.value = "edit";
    modalInitialData.value = row;
    isUserModalOpen.value = true;
}

onMounted(async () => {
    await loadTree();
});
</script>

<template>
    <div class="h-full flex flex-col gap-4 p-2">
        <UpperPanel
            title="Quản lý người dùng"
            icon="fa-solid fa-users"
            :buttons="[
                {
                    label: 'Thêm người dùng',
                    icon: 'fa-solid fa-user-plus',
                    event: 'create-user',
                    class: 'bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 shadow active:scale-95 text-sm font-medium',
                    show: true,
                },
                {
                    label: 'Import người dùng',
                    icon: 'fa-solid fa-file-import',
                    event: 'import-user',
                    class: 'bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow active:scale-95 text-sm font-medium',
                    show: true,
                },
            ]"
            @action="handleAction"
        />

        <div class="flex h-full gap-4 overflow-hidden">
            <LeftPanel
                ref="treePanelRef"
                treeLabel="Danh sách tổ chức"
                :treeData="treeToRender"
                :selected="selectedNode"
                :sortFields="[{ name: 'label', label: 'Tên tổ chức' }]"
                :filterTypes="[]"
                @select="onSelectOrg"
                @search="onTreeSearch"
                @sort="onTreeSort"
                @filter="onTreeFilter"
            />

            <RightPanel
                :key="selectedNode?.id"
                title="Danh sách người dùng"
                icon="fa-solid fa-users"
                :selected="selectedNode"
                api-url=""
                :custom-rows="users"
                :columns="[
                    { key: 'username', label: 'Tên' },
                    { key: 'type.name', label: 'Loại người dùng' },
                    { key: 'status', label: 'Trạng thái' },
                    { key: 'actions', label: 'Hành động', type: 'actions' },
                ]"
                @edit="openEditUserModal"
                @delete="(row) => deleteUser(row.id)"
            />
        </div>

        <UserModal
            v-model="isUserModalOpen"
            :mode="modalMode"
            :initialData="modalInitialData"
            :orgId="selectedNode?.id"
            @submit="handleSubmit"
            @delete="deleteUser"
        />
    </div>
</template>

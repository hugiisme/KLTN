<script setup>
import { ref, onMounted, computed } from "vue";

import LeftPanel from "@/components/Panels/LeftPanel.vue";
import UpperPanel from "@/components/Panels/UpperPanel.vue";
import OrgCard from "./components/OrgCard.vue";
import JoinRequestModal from "./modals/JoinRequestModal.vue";

import useOrgTree from "@/pages/Organizations/composables/useOrgTree";
import OrganizationService from "@/services/OrganizationService.js";
import Notification from "@/services/NotificationService.js";

const treeData = ref([]);
const selectedNode = ref(null);

const isJoinModalOpen = ref(false);
const selectedOrgToJoin = ref(null);

const userMembership = ref({
    joined_org_ids: [],
    pending_org_ids: [],
    exclusive_map: {},
});

function normalizeNode(node, parent = null) {
    if (!node) return null;
    return {
        ...node,
        label: node.label ?? node.name ?? "",
        parent: parent ? { id: parent.id, label: parent.name } : null,
        children: Array.isArray(node.children)
            ? node.children.map((c) => normalizeNode(c, node)).filter(Boolean)
            : [],
    };
}

const {
    treeToRender,
    updateTreeToRender,
    onTreeSearch,
    onTreeFilter,
    onTreeSort,
} = useOrgTree(treeData);

async function loadData() {
    try {
        const [treeRes, statusRes] = await Promise.all([
            OrganizationService.getTree(),
            OrganizationService.getMyStatus().catch(() => ({
                joined_org_ids: [],
                pending_org_ids: [],
                exclusive_map: {},
            })),
        ]);

        treeData.value = treeRes.map((node) => normalizeNode(node));
        updateTreeToRender();

        userMembership.value = statusRes;
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi tải dữ liệu hệ thống");
    }
}

const displayOrgs = computed(() => {
    if (!selectedNode.value) return [];

    return selectedNode.value.children || [];
});

function getUserStatusForOrg(org) {
    if (!userMembership.value || !org) return "available";

    if (userMembership.value.joined_org_ids?.includes(org.id)) return "joined";

    if (userMembership.value.pending_org_ids?.includes(org.id))
        return "pending";

    if (org.org_type && org.org_type.is_exclusive === 1) {
        const typeId = org.org_type.id;
        const joinedOrgIdOfSameType =
            userMembership.value.exclusive_map?.[typeId];

        if (joinedOrgIdOfSameType && joinedOrgIdOfSameType !== org.id) {
            return "blocked_exclusive";
        }
    }

    return "available";
}

function openJoinModal(org) {
    selectedOrgToJoin.value = org;
    isJoinModalOpen.value = true;
}

async function handleConfirmJoin({ org_id, remark }) {
    try {
        console.log("CONFIRM RECEIVED:", { org_id, remark });
        await OrganizationService.sendJoinRequest(org_id, remark ?? null);

        Notification.send("success", "Đã gửi yêu cầu tham gia thành công!");

        userMembership.value.pending_org_ids.push(org_id);
        isJoinModalOpen.value = false;
        selectedOrgToJoin.value = null;
    } catch (e) {
        const errorMsg = e.response?.data?.message || "Gửi yêu cầu thất bại";
        Notification.send("error", errorMsg);
    }
}

onMounted(() => {
    loadData();
});
</script>

<template>
    <div class="h-full flex flex-col gap-4 p-2">
        <UpperPanel
            title="Đăng ký tham gia tổ chức"
            icon="fa-solid fa-handshake"
            :buttons="[]"
        />

        <div class="flex h-full gap-4 overflow-hidden">
            <LeftPanel
                treeLabel="Cơ cấu tổ chức"
                :treeData="treeToRender"
                :selected="selectedNode"
                :sortFields="[{ name: 'name', label: 'Tên' }]"
                :filterTypes="[]"
                @select="(node) => (selectedNode = node)"
                @search="onTreeSearch"
                @sort="onTreeSort"
                @filter="onTreeFilter"
            />

            <div
                class="flex-1 bg-white border border-gray-300 rounded-xl shadow-sm p-5 flex flex-col h-full overflow-hidden"
            >
                <div class="border-b border-gray-200 pb-3 mb-4">
                    <h3
                        class="font-bold text-lg text-gray-800 flex items-center gap-2"
                    >
                        <span v-if="selectedNode">
                            Đang xem:
                            <span class="text-blue-600">{{
                                selectedNode.label
                            }}</span>
                        </span>
                        <span v-else class="text-gray-500 italic">
                            Vui lòng chọn một đơn vị từ danh sách bên trái
                        </span>
                    </h3>
                </div>

                <div
                    v-if="!selectedNode"
                    class="flex-1 flex flex-col items-center justify-center text-gray-400"
                >
                    <i
                        class="fa-solid fa-sitemap text-6xl mb-4 text-gray-200"
                    ></i>
                    <p>Chọn một Khoa/Phòng ban để xem các tổ chức trực thuộc</p>
                </div>

                <div
                    v-else-if="displayOrgs.length === 0"
                    class="flex-1 flex flex-col items-center justify-center text-gray-400"
                >
                    <p>Không tìm thấy tổ chức con nào trực thuộc đơn vị này.</p>
                </div>

                <div
                    v-else
                    class="flex-1 overflow-y-auto pr-2 custom-scrollbar"
                >
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 pb-4"
                    >
                        <OrgCard
                            v-for="org in displayOrgs"
                            :key="org.id"
                            :org="org"
                            :userStatus="getUserStatusForOrg(org)"
                            @register="openJoinModal"
                        />
                    </div>
                </div>
            </div>
        </div>

        <JoinRequestModal
            v-model="isJoinModalOpen"
            :org="selectedOrgToJoin"
            @confirm="handleConfirmJoin"
        />
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

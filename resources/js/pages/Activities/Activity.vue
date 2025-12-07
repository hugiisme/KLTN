<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

// Components
import LeftPanel from "@/components/Panels/LeftPanel.vue";
import RightPanel from "@/components/Panels/RightPanel.vue";
import UpperPanel from "@/components/Panels/UpperPanel.vue";
import ActivityModal from "./modals/ActivityModal.vue";

// Services
import OrganizationService from "@/services/OrganizationService.js";
import ActivityService from "@/services/ActivityService.js";
import Notification from "@/services/NotificationService.js";

// Composables
import useOrgTree from "@/pages/Organizations/composables/useOrgTree";
import useActivities from "./composables/useActivities";

// State
const treeData = ref([]);
const selectedOrg = ref(null);
const treePanelRef = ref(null);
const activityPanelRef = ref(null);
const activityTypes = ref([]);
const activityCategories = ref([]);

// 1. Setup Logic Tree (Left)
const {
    treeToRender,
    updateTreeToRender,
    onTreeSearch,
    onTreeSort,
    onTreeFilter,
    normalizeNode,
} = useOrgTree(treeData);

// 2. Setup Logic Activities (Right)
const {
    isModalOpen,
    modalMode,
    modalInitialData,
    openCreateModal,
    openEditModal,
    handleActivitySubmit,
} = useActivities(selectedOrg, activityPanelRef);

// Load tree data
async function loadTree() {
    try {
        const response = await OrganizationService.getTree();
        treeData.value = response.map((node) => normalizeNode(node));
        updateTreeToRender();
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi tải dữ liệu tổ chức");
    }
}

// Load activity types and categories
async function loadActivityMetadata() {
    try {
        const [types, categories] = await Promise.all([
            axios.get("/api/manage/activity-types"),
            axios.get("/api/manage/activity-categories"),
        ]);
        activityTypes.value = types.data.data ?? [];
        activityCategories.value = categories.data.data ?? [];
    } catch (err) {
        console.error(err);
        Notification.send("error", "Lỗi tải dữ liệu hoạt động");
    }
}

// Handle tree selection
function onTreeSelect(node) {
    selectedOrg.value = node;
}

// Handle Actions from UpperPanel
function handleUpperAction(event) {
    if (event === "create-activity") openCreateModal();
}

// Params Mapper: Chuyển đổi selected node thành params cho API
const apiParams = (node) => {
    if (!node) return {};
    return { org_id: node.id };
};

const statusLabels = {
    draft: "Nháp",
    verified: "Đã xác nhận",
    approved: "Đã duyệt",
    rejected: "Từ chối",
    archived: "Đã lưu trữ",
};

const visibilityLabels = {
    true: "Công khai",
    false: "Ẩn",
    1: "Công khai",
    0: "Ẩn",
};

onMounted(async () => {
    await Promise.all([loadTree(), loadActivityMetadata()]);
});
</script>

<template>
    <div class="h-full flex flex-col gap-4">
        <UpperPanel
            title="Quản lý Hoạt động"
            icon="fa-solid fa-person-running"
            :buttons="[
                {
                    label: 'Thêm hoạt động',
                    icon: 'fa-solid fa-plus',
                    event: 'create-activity',
                    class: 'flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: () => !!selectedOrg, // Chỉ hiện nút thêm khi đã chọn Org
                },
            ]"
            @action="handleUpperAction"
        />

        <div class="flex flex-1 min-h-0 gap-4 overflow-hidden">
            <div class="w-80 shrink-0">
                <LeftPanel
                    ref="treePanelRef"
                    treeLabel="Cơ cấu tổ chức"
                    :treeData="treeToRender"
                    :selected="selectedOrg"
                    :sortFields="[{ name: 'label', label: 'Tên tổ chức' }]"
                    @select="onTreeSelect"
                    @search="onTreeSearch"
                />
            </div>

            <div class="flex-1 min-w-0 flex flex-col gap-4 h-full">
                <RightPanel
                    ref="activityPanelRef"
                    :title="
                        selectedOrg
                            ? `Hoạt động của ${selectedOrg.label}`
                            : 'Danh sách hoạt động'
                    "
                    icon="fa-solid fa-list-check"
                    :selected="selectedOrg"
                    apiUrl="/api/manage/activities"
                    :apiParams="apiParams"
                    :filters="[
                        { name: 'title', label: 'Tên hoạt động' },
                        {
                            name: 'activity_category_id',
                            label: 'Loại hình (Đoàn/NCKH/NVSP)',
                            type: 'select',
                            options: [
                                { value: 1, label: 'Hoạt động Đoàn' },
                                { value: 2, label: 'NCKH' },
                                { value: 3, label: 'NVSP' },
                            ],
                        },
                    ]"
                    :sorters="[
                        { name: 'created_at', label: 'Mới nhất' },
                        { name: 'start_time', label: 'Ngày bắt đầu' },
                    ]"
                    :columns="[
                        {
                            key: 'title',
                            label: 'Tên hoạt động',
                            class: 'font-bold text-gray-700',
                        },
                        {
                            key: 'activity_type.name',
                            label: 'Loại hoạt động',
                            type: 'badge',
                        },
                        {
                            key: 'activity_category.name',
                            label: 'Phân loại',
                            type: 'badge',
                        },
                        {
                            key: 'status',
                            label: 'Trạng thái',
                            type: 'status_badge',
                            formatter: (value) => statusLabels[value] || value,
                        },
                        {
                            key: 'is_visible',
                            label: 'Công khai',
                            type: 'status_badge',
                            formatter: (value) => visibilityLabels[value],
                        },
                        {
                            key: 'start_time',
                            label: 'Bắt đầu',
                            type: 'date',
                        },
                        {
                            key: 'end_time',
                            label: 'Kết thúc',
                            type: 'date',
                        },
                        {
                            key: 'actions',
                            label: 'Hành động',
                            type: 'actions',
                        },
                    ]"
                    @edit="openEditModal"
                    @delete="(row) => console.log('Delete logic here', row)"
                />
            </div>
        </div>
    </div>

    <ActivityModal
        v-model="isModalOpen"
        :mode="modalMode"
        :initialData="modalInitialData"
        :activityTypes="activityTypes"
        :activityCategories="activityCategories"
        @submit="() => activityPanelRef?.reload()"
    />
</template>

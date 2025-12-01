<script setup>
import { ref, onMounted } from "vue";
import LeftPanel from "@/components/Panels/LeftPanel.vue";
import RightPanel from "@/components/panels/RightPanel.vue";
import UpperPanel from "@/components/panels/UpperPanel.vue";

import YearModal from "./modals/AcademicYearModal.vue";
import SemesterModal from "./modals/SemesterModal.vue";

import useAcademicTree from "./composables/useAcademicTree";
import useAcademicYear from "./composables/useAcademicYear";
import useSemester from "./composables/useSemester";

import AcademicYearService from "@/services/AcademicYearService.js";
import Notification from "@/services/NotificationService.js";

const treeData = ref([]);
const selectedNode = ref(null);
const treePanelRef = ref(null);
const semesterPanelRef = ref(null);

// Load tree
async function loadTree() {
    try {
        const res = await AcademicYearService.getTree();
        treeData.value = res;
        updateTreeToRender();
    } catch (err) {
        Notification.send("error", "Lỗi khi load tree năm học - học kỳ");
    }
}

// Composables
const {
    treeToRender,
    academicYears,
    updateTreeToRender,
    onTreeSearch,
    onTreeSort,
    onTreeFilter,
} = useAcademicTree(treeData);

const {
    selectedYear,
    isYearModalOpen,
    modalMode,
    modalInitialData,
    openCreateYearModal,
    openEditYearModal,
    handleYearSubmit,
    onSelectYear,
    deleteYear,
} = useAcademicYear(selectedNode, treeData, loadTree, semesterPanelRef);

const {
    isSemesterModalOpen,
    semesterModalMode,
    semesterModalInitialData,
    openCreateSemesterModal,
    handleSemesterSubmit,
    openEditSemester,
    deleteSemester,
} = useSemester(
    selectedYear,
    treeData,
    loadTree,
    treePanelRef,
    semesterPanelRef
);

function handleUpperAction(event) {
    if (event === "create-year") openCreateYearModal();
    if (event === "edit-year") openEditYearModal();
    if (event === "create-semester") openCreateSemesterModal();
}

onMounted(() => loadTree());
</script>

<template>
    <div class="h-full flex flex-col gap-4">
        <UpperPanel
            title="Quản lý năm học - học kỳ"
            icon="fa-solid fa-graduation-cap"
            :buttons="[
                {
                    label: 'Thêm năm học',
                    icon: 'fa-solid fa-calendar-plus',
                    event: 'create-year',
                    class: 'flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: true,
                },
                {
                    label: 'Sửa năm học',
                    icon: 'fa-solid fa-pen-to-square',
                    event: 'edit-year',
                    class: 'flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: () => !!selectedYear,
                },
                {
                    label: 'Thêm học kỳ',
                    icon: 'fa-solid fa-layer-group',
                    event: 'create-semester',
                    class: 'flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-sm transition-all active:scale-95 text-sm font-medium',
                    show: true,
                },
            ]"
            @action="handleUpperAction"
        />

        <div class="flex flex-1 min-h-0 gap-4">
            <LeftPanel
                ref="treePanelRef"
                treeLabel="Danh sách năm học - học kỳ"
                :treeData="treeToRender"
                :selected="selectedNode"
                :sortFields="[{ name: 'label', label: 'Tên' }]"
                :filterTypes="[
                    { label: 'Năm học', value: 'academic_year' },
                    { label: 'Học kỳ', value: 'semester' },
                ]"
                @select="onSelectYear"
                @search="onTreeSearch"
                @sort="onTreeSort"
                @filter="onTreeFilter"
            ></LeftPanel>

            <div class="flex-1 flex flex-col gap-4 h-full">
                <RightPanel
                    ref="semesterPanelRef"
                    title="Danh sách Học kỳ"
                    icon="fa-solid fa-list"
                    :selected="selectedYear"
                    apiUrl="/api/manage/semesters"
                    :apiParams="(year) => ({ academic_year_id: year.id })"
                    :filters="[{ name: 'name', label: 'Tên học kỳ' }]"
                    :sorters="[
                        { name: 'name', label: 'Tên học kỳ' },
                        { name: 'start_date', label: 'Ngày bắt đầu' },
                        { name: 'end_date', label: 'Ngày kết thúc' },
                    ]"
                    :columns="[
                        { key: 'name', label: 'Tên học kỳ' },
                        {
                            key: 'start_date',
                            label: 'Ngày bắt đầu',
                            type: 'date',
                        },
                        {
                            key: 'end_date',
                            label: 'Ngày kết thúc',
                            type: 'date',
                        },
                        {
                            key: 'actions',
                            label: 'Hành động',
                            type: 'actions',
                        },
                    ]"
                    @edit="openEditSemester"
                    @delete="deleteSemester"
                />
            </div>
        </div>
    </div>

    <YearModal
        v-model="isYearModalOpen"
        :mode="modalMode"
        :initialData="modalInitialData"
        @submit="handleYearSubmit"
        @delete="deleteYear"
    ></YearModal>
    <SemesterModal
        v-model="isSemesterModalOpen"
        :mode="semesterModalMode"
        :initialData="semesterModalInitialData"
        :academicYears="academicYears"
        @submit="handleSemesterSubmit"
    ></SemesterModal>
</template>

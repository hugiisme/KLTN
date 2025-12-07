<script setup>
import { ref, computed, watch, onMounted } from "vue";
import Modal from "@/components/Modal/Modal.vue";
import FormBuilder from "@/components/Form/FormBuilder.vue";
import Notification from "@/services/NotificationService.js";
import ActivityService from "@/services/ActivityService.js";

const props = defineProps({
    modelValue: Boolean,
    mode: { type: String, default: "create" },
    initialData: { type: Object, default: () => ({}) },
    activityTypes: { type: Array, default: () => [] },
    activityCategories: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue", "submit"]);

const formData = ref({});
const loading = ref(false);

const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const modalTitle = computed(() => {
    return props.mode === "create" ? "Thêm hoạt động mới" : "Sửa hoạt động";
});

const formFields = computed(() => [
    {
        name: "title",
        label: "Tên hoạt động",
        type: "text",
        placeholder: "Nhập tên hoạt động",
        required: true,
    },
    {
        name: "detail",
        label: "Mô tả chi tiết",
        type: "textarea",
        placeholder: "Nhập mô tả hoạt động",
        required: false,
    },
    {
        name: "org_id",
        label: "Tổ chức",
        type: "hidden",
    },
    {
        name: "activity_type_id",
        label: "Loại hoạt động",
        type: "select",
        options: props.activityTypes.map((t) => ({
            value: t.id,
            label: t.name,
        })),
        required: true,
    },
    {
        name: "activity_category_id",
        label: "Phân loại",
        type: "select",
        options: props.activityCategories.map((c) => ({
            value: c.id,
            label: c.name,
        })),
        required: true,
    },
    {
        name: "status",
        label: "Trạng thái",
        type: "select",
        options: [
            { value: "draft", label: "Nháp" },
            { value: "verified", label: "Đã xác nhận" },
            { value: "archived", label: "Đã lưu trữ" },
        ],
        required: true,
        default: "draft",
    },
    {
        name: "start_time",
        label: "Thời gian bắt đầu",
        type: "date",
        required: false,
    },
    {
        name: "end_time",
        label: "Thời gian kết thúc",
        type: "date",
        required: false,
    },
    {
        name: "is_visible",
        label: "Hiển thị công khai",
        type: "checkbox",
        default: true,
    },
    {
        name: "semester_id",
        label: "Học kỳ",
        type: "hidden",
    },
    {
        name: "academic_year_id",
        label: "Năm học",
        type: "hidden",
    },
    {
        name: "parent_activity_id",
        label: "Hoạt động cha",
        type: "hidden",
    },
    {
        name: "submission_requirement_id",
        label: "Yêu cầu nộp bài",
        type: "hidden",
    },
]);

function initForm() {
    if (props.mode === "create") {
        formData.value = {
            title: "",
            detail: "",
            org_id: props.initialData?.org_id || "",
            activity_type_id: "",
            activity_category_id: "",
            status: "draft",
            start_time: "",
            end_time: "",
            is_visible: true,
            semester_id: null,
            academic_year_id: null,
            parent_activity_id: null,
            submission_requirement_id: null,
        };
    } else {
        formData.value = { ...props.initialData };
    }
}

async function handleSubmit(data) {
    loading.value = true;
    try {
        if (props.mode === "create") {
            await ActivityService.create(data);
            Notification.send("success", "Thêm hoạt động thành công!");
        } else {
            await ActivityService.update(props.initialData.id, data);
            Notification.send("success", "Cập nhật hoạt động thành công!");
        }
        emit("submit", data);
        modalVisible.value = false;
    } catch (err) {
        const errorMsg =
            err.response?.data?.message || "Xảy ra lỗi khi lưu hoạt động";
        Notification.send("error", errorMsg);
    } finally {
        loading.value = false;
    }
}

watch(
    () => props.initialData,
    () => {
        initForm();
    },
    { deep: true }
);

onMounted(() => {
    initForm();
});
</script>

<template>
    <Modal v-model="modalVisible" :title="modalTitle" width="600px">
        <div class="space-y-4">
            <FormBuilder
                :fields="formFields"
                :initialData="formData"
                submitLabel="Lưu hoạt động"
                @submit="handleSubmit"
            />
        </div>
    </Modal>
</template>

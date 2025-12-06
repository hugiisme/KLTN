<script setup>
import { ref, computed, watch } from "vue";
import Modal from "@/components/Modal/Modal.vue";
import FormBuilder from "@/components/Form/FormBuilder.vue";

import Notification from "@/services/NotificationService.js";

const props = defineProps({
    modelValue: Boolean,
    org: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue", "confirm"]);

const formData = ref({
    remark: "",
});

const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const modalTitle = computed(() => {
    if (!props.org) return "Đăng ký tham gia";
    return `Đăng ký tham gia: ${props.org.name || props.org.label || "..."}`;
});

function validateAndPrepareData(data) {
    const remarkValue = data?.remark ?? formData.value.remark ?? "";
    return remarkValue?.trim() === "" ? null : remarkValue.trim();
}

function handleSubmit(data) {
    if (!props.org || !props.org.id) {
        Notification.send("error", "Lỗi: Không xác định được tổ chức!");
        return;
    }

    const safeRemark = validateAndPrepareData(data);

    Notification.send("info", "Đang gửi yêu cầu...");
    emit("confirm", {
        org_id: props.org.id,
        remark: safeRemark,
    });

    modalVisible.value = false;
}

watch(
    () => props.org,
    () => {
        formData.value.remark = "";
    }
);
</script>

<template>
    <Modal v-model="modalVisible" :title="modalTitle">
        <div
            class="p-4 bg-blue-50 rounded-lg mb-4 text-sm text-blue-800 border border-blue-200"
        >
            <i class="fa-solid fa-info-circle mr-1"></i>
            Bạn đang gửi yêu cầu tham gia tới ban chủ nhiệm của
            <strong>{{ org?.name || org?.label || "tổ chức này" }}</strong
            >. Vui lòng điền lý do hoặc thông tin giới thiệu.
        </div>

        <FormBuilder
            :fields="[
                {
                    name: 'remark',
                    label: 'Lời nhắn / Lý do tham gia',
                    type: 'textarea',
                    placeholder:
                        'Ví dụ: Em muốn tham gia để rèn luyện kỹ năng...',
                    required: false,
                },
            ]"
            :initialData="formData.value"
            submitLabel="Gửi yêu cầu"
            @submit="handleSubmit"
        />
    </Modal>
</template>

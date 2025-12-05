<script setup>
import Modal from "@/components/Modal/Modal.vue";
import FormBuilder from "@/components/Form/FormBuilder.vue";
import { ref, computed, watch } from "vue";
import Notification from "@/services/NotificationService.js";

const props = defineProps({
    modelValue: Boolean,
    org: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue", "confirm"]);

const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const formData = ref({
    remark: "",
});

watch(
    () => props.org,
    () => {
        formData.value.remark = "";
    }
);

const modalTitle = computed(() => {
    if (!props.org) return "Đăng ký tham gia";
    return `Đăng ký tham gia: ${props.org.name || props.org.label || "..."}`;
});

function handleSubmit(data) {
    // Debug log to verify submit is triggered
    console.log("JoinRequestModal submit", data);
    if (!props.org || !props.org.id) {
        Notification.send("error", "Lỗi: Không xác định được tổ chức!");
        return;
    }

    const remarkValue = data?.remark ?? formData.value.remark ?? "";
    const safeRemark = remarkValue?.trim() || "(không có ghi chú)";

    Notification.send("info", "Đang gửi yêu cầu...");
    emit("confirm", {
        org_id: props.org.id,
        remark: safeRemark,
    });

    modalVisible.value = false;
}
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

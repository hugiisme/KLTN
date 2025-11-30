<script setup>
import FormBuilder from "@/components/Form/FormBuilder.vue";
import Modal from "@/components/Modal/Modal.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    modelValue: Boolean,
    mode: { type: String, default: "create" },
    initialData: { type: Object, default: null },
});

const emit = defineEmits(["update:modelValue", "submit"]);

// FIX: tạo v-model proxy
const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const formData = ref({ ...props.initialData });
watch(
    () => props.initialData,
    (newData) => {
        formData.value = { ...newData } || {};
    },
    { immediate: true }
);
</script>

<template>
    <Modal
        v-model="modalVisible"
        :title="props.mode === 'create' ? 'Thêm năm học' : 'Sửa năm học'"
    >
        <FormBuilder
            :fields="[
                { name: 'name', label: 'Tên năm học', type: 'text' },
                { name: 'start_date', label: 'Ngày bắt đầu', type: 'date' },
                { name: 'end_date', label: 'Ngày kết thúc', type: 'date' },
            ]"
            :initialData="formData"
            @submit="emit('submit', $event)"
        />
    </Modal>
</template>

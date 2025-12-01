<script setup>
import FormBuilder from "@/components/Form/FormBuilder.vue";
import Modal from "@/components/Modal/Modal.vue";
import { ref, computed, watch } from "vue";

const props = defineProps({
    modelValue: Boolean,
    mode: { type: String, default: "create" }, // create | edit
    initialData: { type: Object, default: () => ({}) },

    academicYears: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue", "submit"]);

const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

const formData = ref({ ...props.initialData });

watch(
    () => props.initialData,
    (newData) => {
        formData.value = newData ? { ...newData } : {};
    },
    { immediate: true }
);
</script>

<template>
    <Modal
        v-model="modalVisible"
        :title="props.mode === 'create' ? 'Thêm học kỳ' : 'Sửa học kỳ'"
    >
        <input type="hidden" v-model="formData.id" />

        <FormBuilder
            :fields="[
                {
                    name: 'academic_year_id',
                    label: 'Năm học',
                    type: 'select',
                    options: props.academicYears.map((y) => ({
                        value: y.id,
                        label: y.label,
                    })),
                },
                { name: 'name', label: 'Tên học kỳ', type: 'text' },
                { name: 'start_date', label: 'Ngày bắt đầu', type: 'date' },
                { name: 'end_date', label: 'Ngày kết thúc', type: 'date' },
            ]"
            :initialData="formData"
            @submit="
                (payload) => emit('submit', { ...payload, id: formData.id })
            "
        />
    </Modal>
</template>

<!-- resources/js/pages/Organizations/modals/OrganizationModal.vue -->
<script setup>
import FormBuilder from "@/components/Form/FormBuilder.vue";
import Modal from "@/components/Modal/Modal.vue";
import { ref, computed, watch } from "vue";

// Props với default an toàn
const props = defineProps({
    modelValue: Boolean,
    mode: { type: String, default: "create" }, // create | edit
    initialData: { type: Object, default: null },

    // dữ liệu dropdown
    parentOrganizations: { type: Array, default: () => [] },
    orgTypes: { type: Array, default: () => [] },
    orgLevels: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:modelValue", "submit"]);

// Proxy v-model
const modalVisible = computed({
    get: () => props.modelValue,
    set: (val) => emit("update:modelValue", val),
});

// Form data reactive
const formData = ref({ ...props.initialData });

// Watch khi initialData thay đổi
watch(
    () => props.initialData,
    (newData) => {
        formData.value = { ...newData } || {};
    },
    { immediate: true }
);

const parentOptions = computed(() => {
    const items = Array.isArray(props.parentOrganizations)
        ? props.parentOrganizations
        : [];

    if (!items.length) {
        // Nếu CSDL rỗng → disabled
        return [{ value: null, label: "Không có dữ liệu", disabled: true }];
    }

    // Flatten tree
    const flattened = [];
    function walk(arr) {
        for (const n of arr) {
            const name = n.name ?? n.label ?? n.title ?? "";
            if (n.id !== undefined && n.id !== null)
                flattened.push({ value: n.id, label: name });
            if (Array.isArray(n.children) && n.children.length)
                walk(n.children);
        }
    }

    const looksLikeTree = items.some((i) => Array.isArray(i.children));
    if (looksLikeTree) walk(items);
    else
        for (const o of items) {
            const name = o.name ?? o.label ?? "";
            if (o.id !== undefined && o.id !== null)
                flattened.push({ value: o.id, label: name });
        }

    // Luôn thêm option "không có tổ chức cha"

    return [{ value: null, label: "(Không có tổ chức cha)" }, ...flattened];
});

const orgTypeOptions = computed(() => {
    if (!Array.isArray(props.orgTypes))
        return [{ value: null, label: "Không có dữ liệu", disabled: true }];
    return props.orgTypes.length
        ? props.orgTypes.map((t) => ({ value: t.id, label: t.name }))
        : [{ value: null, label: "Không có dữ liệu", disabled: true }];
});

const orgLevelOptions = computed(() => {
    if (!Array.isArray(props.orgLevels))
        return [{ value: null, label: "Không có dữ liệu", disabled: true }];

    return props.orgLevels.length
        ? props.orgLevels.map((l) => ({
              value: l.id,
              label: l.equivalent_name ?? `Level ${l.level_index}`,
          }))
        : [{ value: null, label: "Không có dữ liệu", disabled: true }];
});
</script>

<template>
    <Modal
        v-model="modalVisible"
        :title="props.mode === 'create' ? 'Thêm tổ chức' : 'Sửa tổ chức'"
    >
        <FormBuilder
            :fields="[
                {
                    name: 'name',
                    label: 'Tên tổ chức',
                    type: 'text',
                },
                {
                    name: 'parent_org_id',
                    label: 'Thuộc tổ chức',
                    type: 'select',
                    options: parentOptions,
                    clearable: true,
                },
                {
                    name: 'org_type_id',
                    label: 'Loại tổ chức',
                    type: 'select',
                    options: orgTypeOptions,
                },
                {
                    name: 'org_level_id',
                    label: 'Cấp tổ chức',
                    type: 'select',
                    options: orgLevelOptions,
                },
            ]"
            :initialData="formData"
            @submit="(data) => emit('submit', { ...data, id: formData.id })"
        />
    </Modal>
</template>

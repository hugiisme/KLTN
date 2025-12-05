<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    fields: { type: Array, required: true },
    initialData: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["submit"]);
const formData = ref({});
const errors = ref({});
const openDropdown = ref(null);

function getOptionLabel(field, value) {
    if (!field || !field.options) return "";
    const opt = field.options.find((o) => o.value === value);
    return opt ? opt.label : "";
}

function selectOption(field, value) {
    formData.value[field.name] = value;
    openDropdown.value = null;
}

function onDocClick() {
    openDropdown.value = null;
}

function validateField(field) {
    const value = formData.value[field.name];
    const fieldErrors = [];

    // Check required
    if (field.required && (!value || value === "")) {
        fieldErrors.push(`${field.label} là bắt buộc`);
    }

    // Password validation
    if (field.type === "password" && value) {
        if (value.length < 6) {
            fieldErrors.push(`${field.label} phải có ít nhất 6 kí tự`);
        }
    }

    // Email validation
    if (field.type === "email" && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            fieldErrors.push(`${field.label} không hợp lệ`);
        }
    }

    errors.value[field.name] = fieldErrors;
    return fieldErrors.length === 0;
}

function validateForm() {
    let isValid = true;
    for (const field of props.fields) {
        if (!validateField(field)) {
            isValid = false;
        }
    }
    return isValid;
}

onMounted(() => document.addEventListener("click", onDocClick));
onBeforeUnmount(() => document.removeEventListener("click", onDocClick));

function initForm() {
    const obj = {};
    for (const f of props.fields) {
        if (props.initialData && props.initialData[f.name] !== undefined) {
            obj[f.name] = props.initialData[f.name];
            continue;
        }
        if (f.default !== undefined) {
            obj[f.name] = f.default;
            continue;
        }
        switch (f.type) {
            case "number":
                obj[f.name] = f.default ?? 0;
                break;
            case "checkbox":
                obj[f.name] = f.default ?? false;
                break;
            case "select":
                obj[f.name] =
                    f.default ??
                    (f.options && f.options[0] ? f.options[0].value : "");
                break;
            case "hidden":
                obj[f.name] = f.default ?? "";
                break;
            default:
                obj[f.name] = f.default ?? "";
        }
    }
    formData.value = obj;
    errors.value = {};
}

initForm();

watch(
    () => [props.fields, props.initialData],
    () => {
        initForm();
    },
    { deep: true }
);

function sendForm() {
    if (!validateForm()) {
        return;
    }
    emit("submit", { ...formData.value });
}

// Class chung cho các input để đỡ lặp code
const inputClass =
    "w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-all placeholder-gray-400";
</script>

<template>
    <div class="flex flex-col gap-5">
        <div
            class="flex flex-col gap-1.5"
            v-for="field in fields"
            :key="field.name"
        >
            <input
                v-if="field.type === 'hidden'"
                type="hidden"
                :name="field.name"
                v-model="formData[field.name]"
            />

            <template v-else>
                <label class="font-semibold text-sm text-gray-700 ml-0.5">
                    {{ field.label }}
                </label>

                <input
                    v-if="field.type === 'text'"
                    type="text"
                    :class="inputClass"
                    v-model="formData[field.name]"
                    :placeholder="
                        field.placeholder ||
                        'Nhập ' + field.label.toLowerCase() + '...'
                    "
                />

                <input
                    v-if="field.type === 'password'"
                    type="password"
                    :class="inputClass"
                    v-model="formData[field.name]"
                    :placeholder="
                        field.placeholder ||
                        'Nhập ' + field.label.toLowerCase() + '...'
                    "
                />

                <input
                    v-if="field.type === 'number'"
                    type="number"
                    :class="inputClass"
                    v-model.number="formData[field.name]"
                />

                <input
                    v-if="field.type === 'date'"
                    type="date"
                    :class="inputClass"
                    v-model="formData[field.name]"
                />

                <div v-if="field.type === 'select'" class="relative">
                    <div
                        :class="[
                            inputClass,
                            'flex items-center justify-between cursor-pointer',
                        ]"
                        @click.stop="
                            openDropdown =
                                openDropdown === field.name ? null : field.name
                        "
                    >
                        <span class="truncate">{{
                            getOptionLabel(field, formData[field.name]) ||
                            "Chọn " + field.label
                        }}</span>
                        <i
                            class="fa-solid fa-chevron-down text-xs text-gray-500 ml-2"
                        ></i>
                    </div>

                    <div
                        v-if="openDropdown === field.name"
                        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded shadow-lg max-h-48 overflow-auto"
                    >
                        <div
                            v-for="opt in field.options"
                            :key="String(opt.value) + opt.label"
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                            :class="
                                opt.disabled
                                    ? 'text-gray-400 cursor-not-allowed'
                                    : 'text-gray-800'
                            "
                            @click.stop="
                                !opt.disabled && selectOption(field, opt.value)
                            "
                        >
                            {{ opt.label }}
                        </div>
                    </div>
                </div>

                <textarea
                    v-if="field.type === 'textarea'"
                    :class="inputClass"
                    rows="3"
                    v-model="formData[field.name]"
                ></textarea>

                <div
                    v-if="field.type === 'checkbox'"
                    class="flex items-center gap-2 mt-1"
                >
                    <input
                        type="checkbox"
                        v-model="formData[field.name]"
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                    />
                    <span
                        class="text-sm text-gray-700 cursor-pointer"
                        @click="formData[field.name] = !formData[field.name]"
                        >{{ field.label }}</span
                    >
                </div>

                <div
                    v-if="field.type === 'radio'"
                    class="flex flex-wrap gap-4 mt-1"
                >
                    <label
                        v-for="opt in field.options"
                        :key="opt.value"
                        class="inline-flex items-center gap-2 cursor-pointer"
                    >
                        <input
                            type="radio"
                            :name="field.name"
                            :value="opt.value"
                            v-model="formData[field.name]"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="text-sm text-gray-700">
                            {{ opt.label }}
                        </span>
                    </label>
                </div>
            </template>
        </div>

        <div class="flex flex-col gap-3 mt-4">
            <button
                @click="sendForm"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all active:scale-[0.98]"
            >
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Lưu dữ liệu</span>
            </button>

            <!-- Slot nút custom -->
            <slot name="actions"></slot>
        </div>
    </div>
</template>

<style scoped>
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>

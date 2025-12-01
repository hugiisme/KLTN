<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    fields: { type: Array, required: true },
    initialData: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["submit"]);
const formData = ref({});

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
                    <select
                        :class="[
                            inputClass,
                            'appearance-none cursor-pointer bg-white',
                        ]"
                        v-model="formData[field.name]"
                    >
                        <option
                            v-for="opt in field.options"
                            :key="String(opt.value) + opt.label"
                            :value="opt.value"
                            :disabled="opt.disabled || false"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                    <div
                        class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500"
                    >
                        <i class="fa-solid fa-chevron-down text-xs"></i>
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

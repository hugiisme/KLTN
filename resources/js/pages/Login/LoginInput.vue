<script setup>
import { defineProps, defineEmits, ref, computed } from "vue";

const props = defineProps({
    label: String,
    type: { type: String, default: "text" },
    placeholder: String,
    modelValue: String,
    error: String,
    icon: String,
});

const emit = defineEmits(["update:modelValue"]);
const isFocused = ref(false);
const hasValue = computed(
    () => props.modelValue && props.modelValue.length > 0
);
const isFloating = computed(() => isFocused.value || hasValue.value);
const onFocus = () => (isFocused.value = true);
const onBlur = () => (isFocused.value = false);
const onInput = (e) => emit("update:modelValue", e.target.value);

const showPassword = ref(false);
const inputType = computed(() => {
    if (props.type !== "password") return props.type;
    return showPassword.value ? "text" : "password";
});
const togglePassword = () => {
    if (props.type === "password") {
        showPassword.value = !showPassword.value;
    }
};
</script>

<template>
    <div class="mb-4">
        <div class="relative">
            <input
                :type="inputType"
                :value="modelValue"
                @focus="onFocus"
                @blur="onBlur"
                @input="onInput"
                :placeholder="placeholder"
                class="w-full pr-10 px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black peer"
                :class="
                    error
                        ? 'border-red-500 focus:ring-red-400'
                        : 'border-gray-300 focus:ring-black'
                "
                required
            />

            <label
                :class="[
                    'absolute left-3 transition-all duration-200 pointer-events-none bg-white px-1',
                    isFloating
                        ? 'top-0 -translate-y-3 text-sm text-black'
                        : 'top-1/2 -translate-y-1/2 text-gray-500',
                ]"
            >
                {{ label }}
            </label>

            <i
                class="absolute right-4 top-1/2 -translate-y-1/2 text-lg py-4 px-1"
                :class="[
                    props.type === 'password'
                        ? showPassword
                            ? 'fa-solid fa-lock-open'
                            : 'fa-solid fa-lock'
                        : icon,
                ]"
                @click="togglePassword"
            ></i>
        </div>
        <p v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</p>
    </div>
</template>

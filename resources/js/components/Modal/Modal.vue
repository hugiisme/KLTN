<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    modelValue: Boolean,
    title: String,
    width: {
        type: String,
        default: "500px", // Tăng width mặc định lên xíu cho thoáng
    },
});

const emit = defineEmits(["update:modelValue"]);

// Dùng trực tiếp props để điều khiển v-if bên ngoài hoặc giữ logic cũ
// Tuy nhiên để transition hoạt động tốt, ta nên dùng <Transition> của Vue
function close() {
    emit("update:modelValue", false);
}
</script>

<template>
    <Transition name="modal-fade">
        <div
            v-if="modelValue"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        >
            <div
                class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="close"
            ></div>

            <div
                class="relative bg-white rounded-2xl shadow-2xl w-full transform transition-all flex flex-col max-h-[90vh]"
                :style="{ maxWidth: width }"
            >
                <div
                    class="flex justify-between items-center p-5 border-b border-gray-100"
                >
                    <h2 class="font-bold text-xl text-gray-800">{{ title }}</h2>

                    <button
                        @click="close"
                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
                    >
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <slot />
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
/* Vue Transition Styles */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active .relative,
.modal-fade-leave-active .relative {
    transition: transform 0.2s ease-out;
}

.modal-fade-enter-from .relative,
.modal-fade-leave-to .relative {
    transform: scale(0.95);
}

/* Custom Scrollbar cho modal */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 20px;
}
</style>

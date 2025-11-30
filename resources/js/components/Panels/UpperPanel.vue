<script setup>
const props = defineProps({
    title: { type: String, required: true },
    icon: { type: String, default: "fa-solid fa-circle-info" },

    buttons: {
        type: Array,
        default: () => [],
        /*
            Mỗi button:
            {
                label: "Thêm năm học",
                icon: "fa-solid fa-calendar-plus",
                event: "create-year",
                class: "bg-emerald-600 text-white ...",
                show: boolean hoặc () => boolean
            }
        */
    },
});

const emit = defineEmits(["action"]);
</script>

<template>
    <div
        class="flex justify-between items-center bg-white border border-gray-200 rounded-xl shadow-sm p-4"
    >
        <!-- Left -->
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600"
            >
                <i :class="icon" class="text-lg"></i>
            </div>

            <h2 class="font-bold text-lg text-gray-800">{{ title }}</h2>
        </div>

        <!-- Right: render auto buttons -->
        <div class="flex gap-3">
            <template v-for="(btn, i) in buttons" :key="i">
                <button
                    v-if="
                        typeof btn.show === 'function'
                            ? btn.show()
                            : btn.show !== false
                    "
                    :class="btn.class"
                    @click="emit('action', btn.event)"
                >
                    <i v-if="btn.icon" :class="btn.icon"></i>
                    <span class="ml-1">{{ btn.label }}</span>
                </button>
            </template>
        </div>
    </div>
</template>

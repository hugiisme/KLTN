<script setup>
import { ref, watch } from "vue";
import Modal from "@/components/Modal/Modal.vue";

const props = defineProps({
    modelValue: Boolean,
    org: Object,
    requests: Array,
});

const emit = defineEmits(["update:modelValue"]);
function close() {
    emit("update:modelValue", false);
}
</script>

<template>
    <Modal
        :modelValue="modelValue"
        @update:modelValue="close"
        title="Danh sách chờ duyệt"
        width="600px"
    >
        <div v-if="requests.length">
            <ul class="space-y-2">
                <li
                    v-for="req in requests"
                    :key="req.id"
                    class="p-3 bg-gray-100 rounded"
                >
                    <div class="font-semibold">{{ req.user.name }}</div>
                    <div class="text-sm text-gray-600">
                        {{ req.remark || "Không có ghi chú" }}
                    </div>
                </li>
            </ul>
        </div>

        <div v-else class="text-gray-500 italic text-center">
            Không có yêu cầu chờ duyệt
        </div>
    </Modal>
</template>

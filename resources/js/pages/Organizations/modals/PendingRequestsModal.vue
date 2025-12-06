<script setup>
import { ref, watch } from "vue";
import Modal from "@/components/Modal/Modal.vue";
import OrganizationService from "@/services/OrganizationService.js";
import Notification from "@/services/NotificationService.js";

const props = defineProps({
    modelValue: Boolean,
    org: Object,
    requests: Array,
});

const emit = defineEmits(["update:modelValue", "updated"]); // ⬅ Thêm emit updated khi đổi trạng thái

function close() {
    emit("update:modelValue", false);
}

const loading = ref(null);

async function changeStatus(request, status) {
    loading.value = request.id;

    try {
        await OrganizationService.updateRequestStatus(request.id, status);

        Notification.send("success", "Cập nhật trạng thái thành công!");

        emit("updated"); // ⬅ báo ra parent load lại requests
    } catch (err) {
        Notification.send("error", "Không thể cập nhật trạng thái!");
        console.error(err);
    }

    loading.value = null;
}
</script>

<template>
    <Modal
        :modelValue="modelValue"
        @update:modelValue="close"
        title="Danh sách yêu cầu tham gia"
        width="650px"
    >
        <div v-if="requests.length" class="space-y-3">
            <div
                v-for="req in requests"
                :key="req.id"
                class="border p-3 rounded bg-gray-100 flex justify-between items-center"
            >
                <div>
                    <div class="font-semibold text-sm">
                        {{ req.user.username }}
                        <span
                            class="ml-2 px-2 py-[2px] text-xs rounded"
                            :class="{
                                'bg-yellow-300 text-yellow-900':
                                    req.status === 'pending',
                                'bg-green-300 text-green-900':
                                    req.status === 'approved',
                                'bg-red-300 text-red-900':
                                    req.status === 'rejected',
                            }"
                        >
                            {{ req.status }}
                        </span>
                    </div>
                    <div class="text-gray-600 text-xs italic">
                        {{ req.remark ?? "Không có ghi chú" }}
                    </div>
                </div>

                <div class="flex gap-2">
                    <button
                        class="px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600"
                        @click="changeStatus(req, 'approved')"
                        :disabled="loading === req.id"
                    >
                        Duyệt
                    </button>
                    <button
                        class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"
                        @click="changeStatus(req, 'rejected')"
                        :disabled="loading === req.id"
                    >
                        Từ chối
                    </button>
                    <button
                        class="px-2 py-1 bg-gray-400 text-white text-xs rounded hover:bg-gray-500"
                        @click="changeStatus(req, 'pending')"
                        :disabled="loading === req.id"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="text-gray-500 italic text-center mt-4">
            Không có yêu cầu chờ duyệt
        </div>
    </Modal>
</template>

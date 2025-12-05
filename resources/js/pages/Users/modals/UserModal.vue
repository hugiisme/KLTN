<script setup>
import { ref, computed, watch, onMounted } from "vue";
import Modal from "@/components/Modal/Modal.vue";
import FormBuilder from "@/components/Form/FormBuilder.vue";
import UserService from "@/services/UserService.js";
import OrganizationService from "@/services/OrganizationService.js";
import Notification from "@/services/NotificationService.js";

const props = defineProps({
    modelValue: Boolean,
    mode: { type: String, default: "create" },
    initialData: Object,
    orgId: Number,
});

const emit = defineEmits(["update:modelValue", "submit", "delete"]);

const modalVisible = computed({
    get: () => props.modelValue,
    set: (v) => emit("update:modelValue", v),
});

const formData = ref({});

// LOAD OPTIONS
const userTypes = ref([]);
const organizations = ref([]);

async function handleSubmit(data) {
    try {
        // Filter out empty password for edit mode
        const submitData = { ...data };
        if (props.mode === "edit" && !submitData.password) {
            delete submitData.password;
        }

        if (props.mode === "create") {
            await UserService.create(submitData);
            Notification.send("success", "Đã thêm người dùng mới thành công");
        } else {
            await UserService.update(submitData.id, submitData);
            Notification.send("success", "Đã cập nhật người dùng thành công");
        }
        modalVisible.value = false;
    } catch (error) {
        console.error(error);
        const message =
            error.response?.data?.message ||
            "Lỗi khi lưu người dùng. Vui lòng kiểm tra lại thông tin";
        Notification.send("error", message);
    }
}

function handleDelete() {
    if (confirm("Bạn có chắc chắn muốn xóa người dùng này?")) {
        emit("delete", formData.value.id);
    }
}

async function loadOptions() {
    try {
        userTypes.value = await UserService.getUserTypes();
        organizations.value = await OrganizationService.getAll();
    } catch (e) {
        Notification.send(
            "error",
            "Không tải được dữ liệu user types/organizations"
        );
        console.log(e);
    }
}

watch(
    () => props.initialData,
    (newVal) => {
        formData.value = {
            id: newVal?.id ?? null,
            username: newVal?.username ?? "",
            password: "",
            user_type_id: newVal?.user_type_id ?? "",
            status: newVal?.status ?? "active",
            org_id: props.orgId ?? newVal?.org_id ?? "",
        };
    },
    { immediate: true }
);

onMounted(loadOptions);
</script>

<template>
    <Modal
        v-model="modalVisible"
        :title="mode === 'create' ? 'Thêm người dùng' : 'Sửa người dùng'"
        width="450px"
    >
        <FormBuilder
            :initialData="formData"
            :fields="[
                {
                    name: 'username',
                    label: 'Tên đăng nhập',
                    type: 'text',
                    required: true,
                },
                ...(mode === 'create'
                    ? [
                          {
                              name: 'password',
                              label: 'Mật khẩu',
                              type: 'password',
                              required: true,
                          },
                      ]
                    : [
                          {
                              name: 'password',
                              label: 'Mật khẩu mới',
                              type: 'password',
                              placeholder: '(Để trống nếu không muốn đổi)',
                          },
                      ]),
                {
                    name: 'user_type_id',
                    label: 'Loại người dùng',
                    type: 'select',
                    required: true,
                    options: userTypes.map((u) => ({
                        value: u.id,
                        label: u.name,
                    })),
                },
                {
                    name: 'status',
                    label: 'Trạng thái',
                    type: 'select',
                    required: true,
                    options: [
                        { value: 'active', label: 'Hoạt động' },
                        { value: 'inactive', label: 'Không hoạt động' },
                        { value: 'suspended', label: 'Bị khóa' },
                    ],
                },
                {
                    name: 'org_id',
                    label: 'Thuộc tổ chức',
                    type: 'select',
                    required: true,
                    options: organizations.map((o) => ({
                        value: o.id,
                        label: o.name,
                    })),
                },
            ]"
            @submit="handleSubmit"
        >
            <template v-if="mode === 'edit'" #actions>
                <button
                    @click="handleDelete"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all active:scale-[0.98]"
                >
                    <i class="fa-solid fa-trash"></i>
                    <span>Xóa người dùng</span>
                </button>
            </template>
        </FormBuilder>
    </Modal>
</template>

<script setup>
import { ref } from "vue";

// 1. Dữ liệu giả lập cho Dashboard
const stats = [
    {
        label: "Minh chứng đã lưu",
        value: "12",
        icon: "AcademicCapIcon",
        color: "text-blue-600",
        bg: "bg-blue-100",
    },
    {
        label: "Tổ chức tham gia",
        value: "3",
        icon: "BuildingOfficeIcon",
        color: "text-emerald-600",
        bg: "bg-emerald-100",
    },
    {
        label: "Lượt xem hồ sơ",
        value: "1.2k",
        icon: "EyeIcon",
        color: "text-purple-600",
        bg: "bg-purple-100",
    },
];

const activities = [
    {
        id: 1,
        type: "cert",
        title: "Đã thêm minh chứng: Chứng chỉ IELTS 7.5",
        time: "2 giờ trước",
        icon: "DocumentPlusIcon",
    },
    {
        id: 2,
        type: "org",
        title: "Đã tham gia tổ chức: GDSC HCMC",
        time: "1 ngày trước",
        icon: "UserGroupIcon",
    },
    {
        id: 3,
        type: "security",
        title: "Cập nhật mật khẩu thành công",
        time: "3 ngày trước",
        icon: "ShieldCheckIcon",
    },
];

const recentCertificates = [
    {
        id: 1,
        name: "Giải nhất Hackathon 2024",
        issuer: "FPT University",
        date: "20/11/2024",
    },
    {
        id: 2,
        name: "Khóa học Vue.js Advanced",
        issuer: "Udemy",
        date: "15/10/2024",
    },
];

// Tính phần trăm hoàn thiện hồ sơ
const profileCompletion = 75;
</script>

<template>
    <div class="p-8 min-h-full bg-gray-50/50">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Xin chào, Nguyễn Văn A! 👋
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Đây là tổng quan hồ sơ năng lực của bạn hôm nay.
                </p>
            </div>
            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow hover:bg-blue-700 transition flex items-center gap-2"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4.5v15m7.5-7.5h-15"
                    />
                </svg>
                Thêm minh chứng mới
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                v-for="(stat, index) in stats"
                :key="index"
                class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex items-center gap-4 hover:shadow-md transition"
            >
                <div :class="[stat.bg, stat.color, 'p-3 rounded-lg']">
                    <svg
                        v-if="stat.icon === 'AcademicCapIcon'"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"
                        />
                    </svg>
                    <svg
                        v-else-if="stat.icon === 'BuildingOfficeIcon'"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5M12 6.75h1.5M15 6.75h1.5M9 10.5h1.5M12 10.5h1.5M15 10.5h1.5M9 14.25h1.5M12 14.25h1.5M15 14.25h1.5M9 18h1.5M12 18h1.5M15 18h1.5"
                        />
                    </svg>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ stat.value }}
                    </p>
                    <p class="text-sm text-gray-500">{{ stat.label }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center"
                    >
                        <h3 class="font-semibold text-gray-800">
                            Minh chứng mới nhất
                        </h3>
                        <a
                            href="#"
                            class="text-sm text-blue-600 hover:underline"
                            >Xem tất cả</a
                        >
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div
                            v-for="cert in recentCertificates"
                            :key="cert.id"
                            class="p-6 flex items-center justify-between hover:bg-gray-50 transition"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-5 h-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">
                                        {{ cert.name }}
                                    </h4>
                                    <p class="text-sm text-gray-500">
                                        {{ cert.issuer }} • {{ cert.date }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full"
                                >Đã xác thực</span
                            >
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-800 mb-4">
                        Hoạt động gần đây
                    </h3>
                    <div class="space-y-4 pl-2">
                        <div
                            v-for="act in activities"
                            :key="act.id"
                            class="flex gap-4 relative"
                        >
                            <div
                                class="absolute top-0 bottom-0 left-[19px] w-0.5 bg-gray-200 -z-10 last:hidden"
                            ></div>

                            <div
                                class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center shrink-0"
                            >
                                <span class="text-gray-500 text-lg">
                                    <span v-if="act.type === 'cert'">📜</span>
                                    <span v-else-if="act.type === 'org'"
                                        >🏢</span
                                    >
                                    <span v-else>🔒</span>
                                </span>
                            </div>
                            <div
                                class="bg-white p-4 rounded-lg border border-gray-100 shadow-sm flex-1"
                            >
                                <p class="text-sm font-medium text-gray-800">
                                    {{ act.title }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ act.time }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-8">
                <div
                    class="bg-linear-to-br from-gray-800 to-gray-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group"
                >
                    <div
                        class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition"
                    ></div>

                    <div class="relative z-10">
                        <p
                            class="text-xs font-medium text-gray-300 uppercase tracking-wider mb-4"
                        >
                            Thẻ thành viên
                        </p>
                        <div class="flex items-center gap-4 mb-6">
                            <img
                                src="https://api.dicebear.com/9.x/avataaars/svg?seed=Felix"
                                class="w-14 h-14 rounded-full border-2 border-white/50 bg-white"
                            />
                            <div>
                                <h4 class="font-bold text-lg">Nguyễn Văn A</h4>
                                <p class="text-xs text-gray-300">ID: 8493021</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div
                                class="w-8 h-8 rounded bg-white/20 flex items-center justify-center hover:bg-white/30 cursor-pointer"
                            >
                                FB
                            </div>
                            <div
                                class="w-8 h-8 rounded bg-white/20 flex items-center justify-center hover:bg-white/30 cursor-pointer"
                            >
                                IN
                            </div>
                            <div
                                class="w-8 h-8 rounded bg-white/20 flex items-center justify-center hover:bg-white/30 cursor-pointer"
                            >
                                GH
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
                >
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold text-gray-800">
                            Hoàn thiện hồ sơ
                        </h4>
                        <span class="text-sm font-bold text-blue-600"
                            >{{ profileCompletion }}%</span
                        >
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-4">
                        <div
                            class="bg-blue-600 h-2.5 rounded-full"
                            :style="{ width: profileCompletion + '%' }"
                        ></div>
                    </div>
                    <ul class="space-y-2">
                        <li
                            class="flex items-center gap-2 text-sm text-gray-600"
                        >
                            <svg
                                class="w-4 h-4 text-green-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            Cập nhật Avatar
                        </li>
                        <li
                            class="flex items-center gap-2 text-sm text-gray-600"
                        >
                            <svg
                                class="w-4 h-4 text-green-500"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            Xác thực Email
                        </li>
                        <li
                            class="flex items-center gap-2 text-sm text-gray-400"
                        >
                            <div
                                class="w-4 h-4 rounded-full border border-gray-300"
                            ></div>
                            Liên kết LinkedIn
                        </li>
                    </ul>
                    <button
                        class="mt-4 w-full py-2 text-sm text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50 transition"
                    >
                        Hoàn tất ngay
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

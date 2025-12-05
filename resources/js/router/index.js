import { createRouter, createWebHistory } from "vue-router";
import Home from "@/pages/Home/Home.vue";
import AcademicYears from "@/pages/AcademicYears/AcademicYears.vue";
import Organizations from "@/pages/Organizations/Organizations.vue";
import Account from "@/pages/Accounts/Account.vue";
import Users from "@/pages/Users/Users.vue";
import NotFound from "@/pages/NotFound.vue";
const routes = [
    {
        path: "/",
        component: Home,
        meta: { title: "Trang chủ" },
    },
    {
        path: "/manage/academic_years",
        component: AcademicYears,
        meta: { title: "Quản lý năm học" },
    },
    {
        path: "/manage/organizations",
        component: Organizations,
        meta: { title: "Quản lý tổ chức" },
    },
    {
        path: "/manage/Users",
        component: Users,
        meta: { title: "Quản lý người dùng" },
    },
    {
        path: "/me",
        component: Account,
        meta: { title: "Tài khoản" },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: NotFound,
        meta: { title: "Không tìm thấy trang" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.afterEach((to) => {
    document.title = to.meta.title || "My App";
});

export default router;

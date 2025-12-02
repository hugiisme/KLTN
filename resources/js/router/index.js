import { createRouter, createWebHistory } from "vue-router";
import Home from "@/pages/Home/Home.vue";
import AcademicYears from "@/pages/AcademicYears/AcademicYears.vue";
import Organizations from "@/pages/Organizations/Organizations.vue";
import Account from "@/pages/Accounts/Account.vue";
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
        path: "/me",
        component: Account,
        meta: { title: "Tài khoản" },
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

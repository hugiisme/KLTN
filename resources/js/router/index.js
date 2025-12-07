import { createRouter, createWebHistory } from "vue-router";
import Home from "@/pages/Home/Home.vue";
import AcademicYears from "@/pages/AcademicYears/AcademicYears.vue";
import Organizations from "@/pages/Organizations/Organizations.vue";
import Account from "@/pages/Accounts/Account.vue";
import Users from "@/pages/Users/Users.vue";
import NotFound from "@/pages/NotFound.vue";
import OrgRegistration from "../pages/OrgRegistration/OrgRegistration.vue";
import Activity from "../pages/Activities/Activity.vue";
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
        path: "/organizations/directory",
        component: OrgRegistration,
        meta: { title: "Câu lạc bộ / Đội / Nhóm" },
    },
    {
        path: "/manage/activities",
        component: Activity,
        meta: { title: "Quản lý hoạt động" },
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

// Check if user is authenticated
const isAuthenticated = async () => {
    try {
        const response = await window.axios.get("/api/me");
        return !!response.data;
    } catch (error) {
        return false;
    }
};

router.beforeEach(async (to, from, next) => {
    // List of routes that don't require authentication
    const publicRoutes = ["/login"];

    const isPublic = publicRoutes.includes(to.path);
    const authenticated = await isAuthenticated();

    if (!authenticated && !isPublic) {
        // Redirect to login if not authenticated
        next("/login");
    } else {
        next();
    }
});

router.afterEach((to) => {
    document.title = to.meta.title || "My App";
});

export default router;

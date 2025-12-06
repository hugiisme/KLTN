import Notification from "@/services/NotificationService.js";

export default {
    async logout() {
        try {
            await window.axios.post("/logout");
            Notification.send("success", "Đăng xuất thành công!");
            window.location.href = "/login";
        } catch (error) {
            console.error("Logout failed", error);
            Notification.send("error", "Đăng xuất thất bại");
        }
    },
};

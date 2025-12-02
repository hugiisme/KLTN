import Notification from "@/services/NotificationService.js";

function logout() {
    window.axios
        .post("/logout")
        .then(() => {
            Notification.send("success", "Đăng xuất thành công!");
            window.location.href = "/login";
        })
        .catch((error) => {
            console.error("Logout failed", error);
            Notification.send("error", "Đăng xuất thất bại");
        });
}
export default {
    logout,
};

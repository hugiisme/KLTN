import { onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const SESSION_TIMEOUT = 60 * 60 * 1000; // 1 hour in milliseconds
let timeoutId = null;

export const useSessionTimeout = () => {
    const router = useRouter();

    const logout = async () => {
        try {
            await axios.post("/logout");
        } catch (error) {
            console.error("Logout error", error);
        } finally {
            window.location.href = "/login";
        }
    };

    const resetTimeout = () => {
        // Clear previous timeout
        if (timeoutId) {
            clearTimeout(timeoutId);
        }

        // Set new timeout
        timeoutId = setTimeout(() => {
            logout();
        }, SESSION_TIMEOUT);
    };

    const handleUserActivity = () => {
        resetTimeout();
    };

    onMounted(() => {
        // Set initial timeout
        resetTimeout();

        // Add event listeners for user activity
        document.addEventListener("mousemove", handleUserActivity);
        document.addEventListener("keypress", handleUserActivity);
        document.addEventListener("click", handleUserActivity);
        document.addEventListener("scroll", handleUserActivity);
        document.addEventListener("touchstart", handleUserActivity);
    });

    onUnmounted(() => {
        // Clean up event listeners
        document.removeEventListener("mousemove", handleUserActivity);
        document.removeEventListener("keypress", handleUserActivity);
        document.removeEventListener("click", handleUserActivity);
        document.removeEventListener("scroll", handleUserActivity);
        document.removeEventListener("touchstart", handleUserActivity);

        // Clear timeout
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
    });

    return {
        resetTimeout,
        logout,
    };
};

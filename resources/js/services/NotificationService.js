import { useNotificationsStore } from "../stores/notifications";

export default class NotificationService {
    static async send($type, $message, $timeout = 3000) {
        const store = useNotificationsStore();
        store.push($type, $message, $timeout);
        try {
            await axios.post("/api/log-notification", {
                type: $type,
                message: $message,
            });
        } catch (e) {
            console.error("Cannot write log:", e);
        }
    }
}

import { defineStore } from "pinia";
export const useNotificationsStore = defineStore("notifications", {
    state: () => ({
        notifications: [],
    }),

    actions: {
        push(type, message, timeout = 3000) {
            const id = Date.now();
            this.notifications.push({ id, type, message });

            setTimeout(() => {
                this.remove(id);
            }, timeout);
        },

        remove(id) {
            this.notifications = this.notifications.filter(
                (notification) => notification.id !== id
            );
        },
    },
});

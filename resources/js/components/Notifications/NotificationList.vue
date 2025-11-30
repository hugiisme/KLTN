<script setup>
import { useNotificationsStore } from "../../stores/notifications";
import { storeToRefs } from "pinia";

const store = useNotificationsStore();
const { notifications } = storeToRefs(store);
</script>

<template>
    <div
        class="fixed top-4 right-4 flex flex-col gap-3 z-50 pointer-events-none w-full max-w-sm px-4 md:px-0"
    >
        <div
            v-for="item in notifications"
            :key="item.id"
            class="pointer-events-auto flex overflow-hidden bg-white rounded-lg shadow-lg border-l-4 animate-slide ring-1 ring-black ring-opacity-5 w-full"
            :class="[
                item.type === 'success' && 'border-green-500',
                item.type === 'error' && 'border-red-500',
                item.type === 'warning' && 'border-yellow-500',
                item.type === 'info' && 'border-blue-500',
            ]"
        >
            <div class="flex items-start p-4 w-full">
                <div class="shrink-0">
                    <svg
                        v-if="item.type === 'success'"
                        class="w-6 h-6 text-green-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <svg
                        v-if="item.type === 'error'"
                        class="w-6 h-6 text-red-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <svg
                        v-if="item.type === 'warning'"
                        class="w-6 h-6 text-yellow-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        />
                    </svg>
                    <svg
                        v-if="item.type === 'info'"
                        class="w-6 h-6 text-blue-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p
                        class="text-sm font-medium text-gray-900 first-letter:uppercase"
                    >
                        {{ item.type }}
                    </p>
                    <p
                        class="mt-1 text-sm text-gray-500 text-left wrap-break-word"
                    >
                        {{ item.message }}
                    </p>
                </div>

                <div class="ml-4 shrink-0 flex">
                    <button
                        @click="store.remove(item.id)"
                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <span class="sr-only">Close</span>
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes slide-in-right {
    0% {
        opacity: 0;
        transform: translateX(100%);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}
.animate-slide {
    animation: slide-in-right 0.35s cubic-bezier(0.2, 0.8, 0.2, 1);
}
</style>

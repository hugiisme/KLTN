<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 mt-[-60px]"
    >
        <div class="text-center">
            <h1
                class="text-9xl font-black text-transparent bg-clip-text bg-linear-to-r from-blue-500 to-purple-600 drop-shadow-lg"
            >
                404
            </h1>

            <p class="mt-4 text-2xl font-bold text-gray-800 tracking-tight">
                Oops! Trang không tìm thấy.
            </p>

            <p class="mt-2 text-base text-gray-500 max-w-md mx-auto">
                Có vẻ như trang bạn đang tìm kiếm đã bị di chuyển, xóa hoặc
                đường dẫn không chính xác.
            </p>

            <div class="mt-8">
                <button
                    @click="goHome"
                    class="group relative inline-flex items-center justify-center px-8 py-3 text-base font-medium text-white bg-blue-600 rounded-full shadow-md hover:bg-blue-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 hover:-translate-y-1 hover:shadow-lg"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    Quay về trang chủ
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotFound",
    methods: {
        goHome() {
            this.$router.push("/");
        },
    },
};
</script>
\

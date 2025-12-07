export default [
    // ================== TRANG CHỦ ==================
    {
        name: "Trang chủ",
        url: "/",
    },

    // ================== QUẢN TRỊ HỆ THỐNG ==================
    {
        name: "Quản trị hệ thống",
        url: "",
        children: [
            {
                name: "Năm học – Học kỳ",
                url: "/manage/academic_years",
            },
            {
                name: "Tổ chức",
                url: "/manage/organizations",
            },
            {
                name: "Người dùng",
                url: "/manage/users",
            },
            {
                name: "Phân quyền & vai trò",
                url: "/manage/permissions",
            },
        ],
    },

    // ================== QUẢN LÝ NGHIỆP VỤ ==================
    {
        name: "Quản lý nghiệp vụ",
        url: "",
        children: [
            {
                name: "Hoạt động",
                url: "/manage/activities",
            },
            {
                name: "Đánh giá & Khen thưởng",
                url: "/manage/awards",
            },
        ],
    },

    // ================== THỐNG KÊ & BÁO CÁO ==================
    {
        name: "Thống kê & Báo cáo",
        url: "",
        children: [
            {
                name: "Tổng hợp số liệu",
                url: "/reports/overview",
            },
            {
                name: "Báo cáo chi tiết",
                url: "/reports/export",
            },
        ],
    },

    // ================== HOẠT ĐỘNG NGƯỜI DÙNG ==================
    {
        name: "Hoạt động",
        url: "",
        children: [
            {
                name: "Tổng hợp hoạt động",
                url: "/activities",
            },
            {
                name: "Đề tài NCKH",
                url: "/activities/research",
            },
        ],
    },

    // ================== TỔ CHỨC ==================
    {
        name: "Tổ chức",
        url: "",
        children: [
            {
                name: "Khám phá tổ chức",
                url: "/organizations/directory",
            },
            {
                name: "Tổ chức tôi tham gia",
                url: "/organizations/my-orgs",
            },
        ],
    },

    // ================== THI ĐUA - CÁ NHÂN ==================
    {
        name: "Thi đua & Thành tích",
        url: "",
        children: [
            {
                name: "Các đợt thi đua",
                url: "/awards/rounds",
            },
            {
                name: "Thành tích của tôi",
                url: "/awards/my-achievements",
            },
        ],
    },

    // ================== THÔNG BÁO & TƯƠNG TÁC ==================
    {
        name: "Thông báo",
        url: "",
    },
];

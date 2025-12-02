export default [
    {
        name: "Trang chủ",
        url: "/",
    },
    {
        name: "Quản trị",
        url: "",
        children: [
            {
                name: "Quản lý năm học",
                url: "/manage/academic_years",
            },
            {
                name: "Quản lý tổ chức",
                url: "/manage/organizations",
            },
        ],
    },
    {
        name: "Hoạt động",
        url: "/activities",
        children: [
            {
                name: "Sự kiện sắp tới",
                url: "/activities/upcoming",
            },
            {
                name: "Hoạt động của tôi",
                url: "/activities/my-activities", // Xem lịch sử, nộp minh chứng hoạt động
            },
            {
                name: "Nghiên cứu khoa học",
                url: "/activities/research", // Đăng ký và quản lý đề tài NCKH
            },
        ],
    },
    {
        name: "Tổ chức & CLB",
        url: "/organizations",
        children: [
            {
                name: "Danh bạ CLB/Đội/Nhóm",
                url: "/organizations/directory", // Nơi đăng ký tham gia CLB (is_exclusive = false)
            },
            {
                name: "Tổ chức của tôi",
                url: "/organizations/my-orgs", // Xem các tổ chức mình đang là thành viên
            },
        ],
    },
    {
        name: "Thi đua & Khen thưởng",
        url: "/awards",
        children: [
            {
                name: "Đợt thi đua",
                url: "/awards/rounds", // Xem và nộp hồ sơ xét duyệt (SV5T,...)
            },
            {
                name: "Thành tích của tôi",
                url: "/awards/my-achievements", // Xem các danh hiệu đã đạt được
            },
        ],
    },
];

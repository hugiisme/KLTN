export default [
    {
        name: "Trang chủ",
        url: "/",
    },

    // ================== QUẢN TRỊ ==================
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
                name: "Nhóm người dùng",
                url: "/manage/user-groups",
            },

            {
                name: "Nghiệp vụ",
                url: "",
                children: [
                    {
                        name: "Hoạt động",
                        url: "/manage/activities",
                    },
                    {
                        name: "Thi đua",
                        url: "/manage/awards",
                    },
                    {
                        name: "Nghiên cứu khoa học",
                        url: "/manage/research",
                    },
                ],
            },
        ],
    },

    // ================== HOẠT ĐỘNG ==================
    {
        name: "Hoạt động",
        url: "",
        children: [
            {
                name: "Tổng hợp hoạt động",
                url: "/activities",
            },
            {
                name: "Hoạt động của tôi",
                url: "/activities/my-activities",
            },
            {
                name: "Đề tài NCKH",
                url: "/activities/research",
            },
        ],
    },

    // ================== TỔ CHỨC ==================
    {
        name: "CLB – Đội – Nhóm",
        url: "/organizations",
        children: [
            {
                name: "Khám phá CLB",
                url: "/organizations/directory",
            },
            {
                name: "Tổ chức tôi tham gia",
                url: "/organizations/my-orgs",
            },
        ],
    },

    // ================== Khen thưởng ==================
    {
        name: "Thi đua & Khen thưởng",
        url: "/awards",
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
];

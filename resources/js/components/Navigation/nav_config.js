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
];

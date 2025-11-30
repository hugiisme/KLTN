import axios from "axios";
export default {
    getAll: () => axios.get("/api/manage/org-levels").then((r) => r.data),
};

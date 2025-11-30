import axios from "axios";
export default {
    getAll: () => axios.get("/api/manage/org-types").then((r) => r.data),
};

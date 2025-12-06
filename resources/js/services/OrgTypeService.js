import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/org-types");
        return response.data.data ?? [];
    },
};

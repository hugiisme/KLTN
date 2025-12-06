import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/org-levels");
        return response.data.data ?? [];
    },
};

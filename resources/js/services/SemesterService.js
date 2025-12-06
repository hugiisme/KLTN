import axios from "axios";

export default {
    async getByYear(yearId) {
        const response = await axios.get("/api/manage/semesters", {
            params: { academic_year_id: yearId },
        });
        return response.data.data ?? [];
    },

    async create(data) {
        return axios.post("/api/manage/semesters", data);
    },

    async update(id, data) {
        return axios.put(`/api/manage/semesters/${id}`, data);
    },

    async delete(id) {
        return axios.delete(`/api/manage/semesters/${id}`);
    },
};

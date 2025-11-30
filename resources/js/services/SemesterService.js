import axios from "axios";

export default {
    async getByYear(yearId) {
        return (
            await axios.get("/api/manage/semesters", {
                params: { academic_year_id: yearId },
            })
        ).data;
    },

    async create(data) {
        return axios.post("/api/manage/semesters", data);
    },

    async update(id, data) {
        return axios.put(`/api/manage/semesters/${id}`, data);
    },
};

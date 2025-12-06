import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/academic-years/tree");
        return response.data.data;
    },

    async create(data) {
        return axios.post("/api/manage/academic-years", data);
    },

    async update(id, data) {
        return axios.put(`/api/manage/academic-years/${id}`, data);
    },

    async delete(id) {
        return axios.delete(`/api/manage/academic-years/${id}`);
    },
};

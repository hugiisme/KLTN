import axios from "axios";

export default {
    async getTree() {
        return (await axios.get("/api/manage/academic-years/tree")).data;
    },

    async createYear(data) {
        return axios.post("/api/manage/academic-years", data);
    },

    async updateYear(id, data) {
        return axios.put(`/api/manage/academic-years/${id}`, data);
    },

    async deleteYear(id) {
        return axios.delete(`/api/manage/academic-years/${id}`);
    },
};

import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/organizations");
        return response.data.data ?? [];
    },

    async getTree() {
        return (await axios.get("/api/manage/organizations/tree")).data;
    },

    async create(data) {
        return axios.post("/api/manage/organizations", data);
    },

    async update(id, data) {
        return axios.put(`/api/manage/organizations/${id}`, data);
    },

    async delete(id) {
        return axios.delete(`/api/manage/organizations/${id}`);
    },

    async getTypes() {
        return (await axios.get("/api/manage/org-types")).data;
    },

    async getLevels() {
        return (await axios.get("/api/manage/org-levels")).data;
    },

    async getById(id) {
        const res = await axios.get(`/api/manage/organizations/${id}`);
        return res.data;
    },
};

import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/users");
        return response.data.data ?? [];
    },

    async getByOrg(orgId) {
        const response = await axios.get(
            `/api/manage/organizations/${orgId}/users`
        );
        return response.data.data ?? [];
    },

    async create(data) {
        return axios.post("/api/manage/users", data);
    },

    async update(id, data) {
        return axios.put(`/api/manage/users/${id}`, data);
    },

    async delete(id) {
        return axios.delete(`/api/manage/users/${id}`);
    },

    async getTypes() {
        const response = await axios.get("/api/manage/user-types");
        return response.data.data ?? [];
    },
};

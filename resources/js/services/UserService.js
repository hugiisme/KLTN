import axios from "axios";

export default {
    async getByOrg(orgId) {
        const response = await axios.get(`/api/organizations/${orgId}/users`);
        return response.data.data ?? [];
    },

    async create(data) {
        return axios.post("/api/users", data);
    },

    async update(id, data) {
        return axios.put(`/api/users/${id}`, data);
    },

    async delete(id) {
        return axios.delete(`/api/users/${id}`);
    },
    async getUserTypes() {
        return (await axios.get("/api/user-types")).data;
    },
};

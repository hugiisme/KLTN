import axios from "axios";

export default {
    async getAll() {
        const response = await axios.get("/api/manage/organizations");
        return response.data.data ?? [];
    },

    async getTree() {
        const response = await axios.get("/api/manage/organizations/tree");
        return response.data.data ?? [];
    },

    async getById(id) {
        const res = await axios.get(`/api/manage/organizations/${id}`);
        return res.data.data ?? {};
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
        const response = await axios.get("/api/manage/org-types");
        return response.data.data ?? [];
    },

    async getLevels() {
        const response = await axios.get("/api/manage/org-levels");
        return response.data.data ?? [];
    },

    async getMyStatus() {
        const res = await axios.get("/api/me/org-status");
        return res.data.data ?? {};
    },

    async sendJoinRequest(orgId, remark) {
        return axios.post("/api/me/join-requests", {
            org_id: orgId,
            remark: remark,
        });
    },

    async getPendingRequests(orgId) {
        const response = await axios.get(
            `/api/manage/organizations/${orgId}/pending-requests`
        );
        return response.data.data ?? [];
    },
};

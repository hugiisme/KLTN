import axios from "axios";

class ActivityService {
    static getAll(filters = {}) {
        const params = new URLSearchParams();

        if (filters.org_id) params.append("org_id", filters.org_id);
        if (filters.search) params.append("search", filters.search);
        if (filters.filter_field)
            params.append("filter_field", filters.filter_field);
        if (filters.filter_value)
            params.append("filter_value", filters.filter_value);
        if (filters.sort_field) params.append("sort_field", filters.sort_field);
        if (filters.sort_direction)
            params.append("sort_direction", filters.sort_direction);
        if (filters.page) params.append("page", filters.page);
        if (filters.per_page) params.append("per_page", filters.per_page);

        return axios.get(`/api/manage/activities?${params}`);
    }

    static getById(id) {
        return axios.get(`/api/manage/activities/${id}`);
    }

    static create(data) {
        return axios.post("/api/manage/activities", data);
    }

    static update(id, data) {
        return axios.put(`/api/manage/activities/${id}`, data);
    }

    static delete(id) {
        return axios.delete(`/api/manage/activities/${id}`);
    }
}

export default ActivityService;

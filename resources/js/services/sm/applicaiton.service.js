import axios from "axios"

const BASE_URL = '/api/sm/applications';
export default {
    save: async(form) => {
        return axios.post(`${BASE_URL}`,form)
    },
    detail: async(id) => {
        return axios.get(`${BASE_URL}/${id}`)
    },
    update: async(form,id) => {
        return axios.patch(`${BASE_URL}/${id}`,form)
    }
}
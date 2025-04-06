import axios from "axios"
const baseUrl = '/pa/insured-persons';

export default {
    save: (data) => {
        return axios.post(`${baseUrl}`,data)
    },
    update: (data, id) => {
        return axios.post(`${baseUrl}/${id}`, data)
    },
    delete: (id) => {
        return axios.delete(`${baseUrl}/${id}`)
    },
    deleteMulti: (data) => {
        return axios.post(`${baseUrl}/delete-multi`, data)
    }
}
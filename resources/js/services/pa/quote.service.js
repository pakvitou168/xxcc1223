import axios from "axios"

const baseUrl = '/pa/quotations';
const actionUrl = '/pa/quotation';
export default {
    
    save: (form) => {
        return axios.post(`${baseUrl}`, form)
    },
    delete: (id) => {
        return axios.delete(`${baseUrl}/${id}`)
    },
    detail: (id) => {
        return axios.get(`${baseUrl}/${id}`)
    },
    approve: (id,form) => {
        return axios.patch(`${actionUrl}/${id}/approve`,form)
    },
    accept: (id,form) => {
        return axios.patch(`${actionUrl}/${id}/accept`,form)
    },
    proceed: (id) => {
        return axios.get(`${actionUrl}/${id}/proceed`)
    },
    edit: (id) => {
        return axios.get(`${baseUrl}/${id}/edit`)
    },
    update: (data,id) => {
        return axios.post(`${baseUrl}/${id}`,data)
    },
}
import axios from "axios"

const serviceUrl = '/travel/quotation-service';
const baseUrl = '/travel/quotations';
const actionUrl = '/travel/quotations';
export default {
    selection: () => {
        return axios.get(`${serviceUrl}/selections`)
    },
    searchInsuredPs: (form) => {
        return axios.post(`${serviceUrl}/search-insured-persons`, form)
    },
    validateFileUpload: (form) => {
        return axios.post(`${serviceUrl}/validate-file`, form)
    },
    filterBusiness: (businessChannel) => {
        return axios.get(`${serviceUrl}/business-options/${businessChannel}`)
    },
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
    }
}
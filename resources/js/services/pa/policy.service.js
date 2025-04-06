
import axios from "axios"

const baseUrl = '/pa/policies';

export default {
    detail: (id) => {
        return axios.get(`${baseUrl}/${id}`)
    },
    commission: (id) => {
        return axios.get(`${baseUrl}/${id}/commission`)
    },
    updateCommission: (data, id) => {
        return axios.patch(`${baseUrl}/${id}/update-commission`, data)
    },
    config: (id) => {
        return axios.get(`${baseUrl}/${id}/config`)
    },
    updateConfig: (data, id) => {
        return axios.patch(`${baseUrl}/${id}/update-config`, data)
    },
    reinsurance: (id, params) => {
        return axios.get(`${baseUrl}/${id}/reinsurance`, { params: params })
    },
    edit: (id) => {
        return axios.get(`${baseUrl}/${id}/edit`);
    },
    delete: (id) => {
        return axios.delete(`${baseUrl}/${id}`)
    },
    updateInsurance: (data,id) => {
        return axios.patch(`${baseUrl}/${id}/update-reinsurance`,data);
    },
    approve: (data,id) =>{
        return axios.patch(`${baseUrl}/${id}/approve`,data);
    },
    endorse: (data,id) => {
        return axios.patch(`${baseUrl}/${id}/endorse`,data);
    },
    // Add to policy.service.js
    print: (id, lang, options = {}) => {
        const queryParams = new URLSearchParams();
        if (options.letterhead) queryParams.append('letterhead', options.letterhead);
        if (options.stamp !== undefined) queryParams.append('stamp', options.stamp);

        return `/pa/policy/${id}/download/${lang}?${queryParams.toString()}`;
    },

    downloadInvoice: (id, withSignature = false) => {
        const signatureParam = withSignature ? '/with-signature' : '';
        return `/pa/policy/${id}/download-invoice${signatureParam}`;
    },

    downloadCertificate: (id) => {
        return `/pa/policy/${id}/download-certificate`;
    }
}

import axios from "axios"

const baseUrl = '/travel/policies';
export default {
    isPolicyConfigurationCompleted: (id) => {
        return axios.get(`${baseUrl}/is-policy-configuration-completed/${id}`)
    },
    isPolicyReinsuranceCompleted: (id) => {
        return axios.get(`${baseUrl}/is-policy-reinsurance-completed/${id}`);
    },
    updateSubmitStatus: (id, form) => {
        return axios.put(`${baseUrl}/update-submit-status/${id}`,form );
    },
    approve: (id, form) => {
        return axios.put(`${baseUrl}/approve/${id}`,form );
    },

}
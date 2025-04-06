import axios from "axios"

const BASE_URL = '/travel/policies';
export default {
    delete: (id) => {
        return axios.delete(`${BASE_URL}/${id}`)
    },
    detail: (id) => {
        return axios.get(`${BASE_URL}/${id}`)
    },
    getLovs: () => {
        return axios.get(`${BASE_URL}/get-lovs`);
    },
    update:(form,id) =>{
        return axios.put(`${BASE_URL}/${id}`,form)
    },
    updateIssueDate:(form,id) =>{
        return axios.put(`${BASE_URL}/update-issue-date/${id}`,form)
    }

}
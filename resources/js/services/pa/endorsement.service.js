import axios from "axios"

const baseUrl = '/pa/endorsements';
export default {
    edit: (id) => {
        return axios.get(`${baseUrl}/${id}/edit`)
    },
    info: (id) => {
        return axios.get(`${baseUrl}/${id}/info`)
    },
    update: (data, id) => {
        return axios.patch(`${baseUrl}/${id}`, data)
    }
}
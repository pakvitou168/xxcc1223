import axios from "axios"

const baseUrl = '/travel/policies';
export default {
    get: (id) => {
        return axios.get(`${baseUrl}/get-deductible-data/${id}`)
    },

}
import axios from "axios"
const BASE_URL = '/api/sm'

export default {
    status : () => {
        return axios.get(`${BASE_URL}/status`)
    }
}
import axios from "axios"

const BASE_URL = '/hs/clinics';
export default {
    store: (form, method) => {
        if (method === "POST") return axios.post(`${BASE_URL}`, form);
        else{
            return axios.patch(`${BASE_URL}/${form.id}`, form);
        }
    },
}
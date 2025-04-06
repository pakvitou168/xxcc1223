import axios from "axios"

const baseUrl = '/clause-maintenance-service'
export default{
    clauseType : (productLine) => {
        return axios.get(`${baseUrl}/clause-type/${productLine}`)
    }
}
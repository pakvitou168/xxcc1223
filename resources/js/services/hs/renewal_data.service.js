const BASE_URL = '/hs/reinsurance-data'

export default {
  detail: (id) => {
    return axios.get(`${BASE_URL}/${id}`)
  },
  update:(form,id)=>{
    return axios.put(`${BASE_URL}/${id}`,form)
  },
  save:(form)=>{
    return axios.post(`${BASE_URL}`,form)
  },
  getSum:(policyId)=>{
    return axios.get(`${BASE_URL}/${policyId}/get-sum`)
  }
}
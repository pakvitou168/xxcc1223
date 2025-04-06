const BASE_URL = '/hs/policies'

export default {
  detail: (id) => {
    return axios.get(`${BASE_URL}/${id}`)
  },
  update:(form,id)=>{
    return axios.put(`${BASE_URL}/${id}`,form)
  }
}
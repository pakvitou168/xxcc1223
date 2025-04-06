const RESOURCE = '/product-config/product-condition-rating'

export default {
  save: (form, method) => {
    if (method === 'POST')
      return axios.post(`${RESOURCE}`, form);
    else if (method === 'PUT') {
      return axios.put(`${RESOURCE}/${form.id}`, form);
    }
  },
  getData: id => {
    return axios.get(`${RESOURCE}/${id}`)
  },
  delete: id => {
    return axios.delete(`${RESOURCE}/${id}`)
  },
}
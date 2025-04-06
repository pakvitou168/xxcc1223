const RESOURCE = '/deductibles'

export default {
  save: (form, method) => {
    if (method === 'POST')
        return axios.post(`${RESOURCE}`, form);
    else if (method === 'PUT') {
        return axios.put(`${RESOURCE}/${form.id}`, form);
    }
  },
  getLovs: () => {
    return axios.get(`/deductibles-lovs`);
  },
  listProducts: (productLineCode) => {
    return axios.get(`/deductibles-list-products/${productLineCode}`);
  },
  listCovers: (productCode) => {
    return axios.get(`/deductibles-list-covers/${productCode}`);
  },
  getData: id => {
    return axios.get(`${RESOURCE}/${id}`)
  },
  delete: id => {
    return axios.delete(`${RESOURCE}/${id}`)
  },
}
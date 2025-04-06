const RESOURCE = '/claim-cause-of-losses'

export default {
  save: (form, method) => {
    if (method === 'POST')
        return axios.post(`${RESOURCE}`, form);
    else if (method === 'PUT') {
        return axios.put(`${RESOURCE}/${form.id}`, form);
    }
  },
  getLovs: () => {
    return axios.get(`/claim-cause-of-losses-lovs`);
  },
  listProducts: (productLineCode) => {
    return axios.get(`/claim-cause-of-losses-list-products/${productLineCode}`);
  },
  getData: id => {
    return axios.get(`${RESOURCE}/${id}`)
  },
  delete: id => {
    return axios.delete(`${RESOURCE}/${id}`)
  },
}
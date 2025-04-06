const RESOURCE = '/claim-recoveries'

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
  approve: (form, id) => {
    return axios.post(`${RESOURCE}/${id}/approve`, form)
  },
  getLovs: () => {
    return axios.get('/claim-recoveries-lovs');
  },
  printUrl: (id, lang, hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true`
    }
    return url
  },
}
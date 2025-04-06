const RESOURCE = '/claim-registers'

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
  revise: id => {
    return axios.post(`${RESOURCE}/${id}/revise`);
  },
  detail: id => {
    return axios.get(`${RESOURCE}/${id}/detail`)
  },
  getLovs: () => {
    return axios.get('/claim-register-lovs');
  },
  listVehicles: policyDocNo => {
    return axios.get(`/claim-register-list-vehicles/${policyDocNo}`)
  },
  listCovers: detailId => {
    return axios.get(`/claim-register-list-covers/${detailId}`)
  },
  printUrl: (id, lang, hasLetterhead) => {
    let url = `/claim-registers/${id}/print/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true`
    }
    return url
  },
  listDeductibles: detailId => {
    return axios.get(`/claim-register-list-deductible/${detailId}`)
  },
}
const RESOURCE = '/claim-partial-payments'

export default {
  save: (form, method) => {
    if (method === 'POST')
        return axios.post(`${RESOURCE}`, form);
    else if (method === 'PUT') {
        return axios.put(`${RESOURCE}/${form.id}`, form);
    }
  },
  detail: id => {
    return axios.get(`${RESOURCE}/${id}/detail`)
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
  getLovs: () => {
    return axios.get('/claim-partial-payments-lovs');
  },
  listCauseOfLosses: claimNo => {
    return axios.get(`/claim-partial-payments-list-cause-of-losses/${claimNo}`)
  },
  getVehicle: detailId => {
    return axios.get(`/claim-partial-vehicle/${detailId}`)
  },
  savePaymentNumbers: id => {
    return axios.put(`${RESOURCE}/${id}/save-payment-numbers`);
  },
  havePaymentNumbers: id => {
    return axios.get(`${RESOURCE}/${id}/have-payment-numbers`);
  },
  printChequeUrl: (id, lang,hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print-cheque/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true`
    }
    return url
  },
  printPaymentUrl: (id, lang, hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true`
    }
    return url
  },
}
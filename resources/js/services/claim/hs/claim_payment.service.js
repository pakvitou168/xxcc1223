const RESOURCE = '/hs/claim-payment'

export default {
  save: (form, method) => {
    if (method === 'POST')
      return axios.post(`${RESOURCE}/payments`, form);
    else if (method === 'PUT') {
      return axios.put(`${RESOURCE}/payments/${form.id}`, form);
    }
  },
  getData: id => {
    return axios.get(`${RESOURCE}/payments/${id}/edit`)
  },
  delete: id => {
    return axios.delete(`${RESOURCE}/payments/${id}`)
  },
  approve: (form, id) => {
    return axios.post(`${RESOURCE}/payments/${id}/approve`, form)
  },
  revise: id => {
    return axios.post(`${RESOURCE}/payments/${id}/revise`);
  },
  detail: id => {
    return axios.get(`${RESOURCE}/payments/${id}`)
  },
  getLovs: search => {
    return axios.get(`${RESOURCE}/get-lovs`, {
      params: {
        search: search
      }
    });
  },
  printDischargeUrl: (id, lang,hasLetterhead) => { 
    let url = `${RESOURCE}/${id}/print-discharge/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true&print=discharge`
    }
    return url + `?print=discharge`
  },
  printChequeUrl: (id, lang,hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print-cheque/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true&print=cheque`
    }
    return url + `?print=cheque`
  },
  printRevisionUrl: (id, lang,hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print-revision/${lang}`
    if (hasLetterhead) {
      return url + `?letterhead=true&print=revision`
    }
    return url
  },
  printUrl: (id, lang, hasLetterhead) => {
    let url = `${RESOURCE}/${id}/print/${lang}`
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
  savePaymentNumbers: id => {
    return axios.put(`${RESOURCE}/${id}/save-payment-numbers`);
  },
  listCauseOfLosses: claimNo => {
    return axios.get(`${RESOURCE}/claim-payments-list-cause-of-losses/${claimNo}`)
  },
}
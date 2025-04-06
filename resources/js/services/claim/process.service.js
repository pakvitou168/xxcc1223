import axios from "axios";

const RESOURCE = '/claim-processes'

export default {
  save: (form, method) => {
    if (method === 'POST')
        return axios.post(`${RESOURCE}`, form);
    else if (method === 'PUT') {
        return axios.put(`${RESOURCE}/${form.id}`, form);
    }
  },
  previewDeductible: (form) => {
        return axios.post(`/claim-processes-preview-deductibles`, form);
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
    return axios.get('/claim-processes-lovs');
  },
  listCauseOfLosses: claimNo => {
    return axios.get(`/claim-processes-list-cause-of-losses/${claimNo}`)
  },
  detail: id => {
    return axios.get(`${RESOURCE}/${id}/detail`)
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
  getVehicle:detailId=>{
    return axios.get(`/claim-processes-get-vehicle/${detailId} `)
  },
  savePaymentNumbers: id => {
    return axios.put(`${RESOURCE}/${id}/save-payment-numbers`);
  },
  havePaymentNumbers: id => {
    return axios.get(`${RESOURCE}/${id}/have-payment-numbers`);
  },
  generateRecovery: id => {
    return axios.post(`${RESOURCE}/${id}/generate-recovery`)
  },
  alreadyGeneratedRecovery: claimNo => {
    return axios.get(`${RESOURCE}/${claimNo}/already-generated-recovery`)
  }
}
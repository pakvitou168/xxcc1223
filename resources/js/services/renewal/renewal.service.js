const RESOURCE = '/api/renewals'

export default {
  generateRenewalList: (underwritingYear, expiredFromDate, expiredToDate) => {
    return axios.post(`${RESOURCE}/generate-renewal-list`, {
      uw_year: underwritingYear,
      expired_date_from: expiredFromDate,
      expired_date_to: expiredToDate
    })
  },
  autoApproveNoClaimPolicies: () => {
    return axios.post(`${RESOURCE}/auto-approve-no-claim-policies`)
  },
  generateRenewedPolicy: id => {
    return axios.post(`${RESOURCE}/generate-renewed-policy/${id}`)
  },
  canGenerateRenewedPolicy: id => {
    return axios.post(`${RESOURCE}/can-generate-renewed-policy/${id}`)
  },
  approve: (form, id) => {
    return axios.post(`${RESOURCE}/${id}/approve`, form)
  },
  accept: (form, id) => {
    return axios.post(`${RESOURCE}/${id}/accept`, form)
  },
  revise: id => {
    return axios.post(`${RESOURCE}/${id}/revise`)
  },
  statusLovs: () => axios.get(`${RESOURCE}/status-lovs`),
  generateNewVersion: id => {
    return axios.post(`${RESOURCE}/generate-new-version/${id}`)
  },
  edit: id => {
    return axios.get(`${RESOURCE}/${id}/edit`)
  },
  submit: id => {
    return axios.post(`${RESOURCE}/${id}/submit`)
  },
  export: form => {
    return axios.post(`${RESOURCE}/export`, form, {
      responseType: 'blob'
    })
  },
}
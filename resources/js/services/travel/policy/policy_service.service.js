const BASE_URL = '/travel/policies/policy-services'

export default {
  getSignature: (id) => {
    return axios.get(`${BASE_URL}/${id}/get-signature`)

  },
  getBusinessNameByBusinessCode: (businessCode) => {
    return axios.get(`${BASE_URL}/get-business-name-by-business-code/${businessCode}`)
  },
  updateCommissionData: (form, id) => {
    return axios.post(`${BASE_URL}/update-commission-data/${id}`, form)
  },
  isCommissionDataAvailable: (detailId) => {
    return axios.get(`${BASE_URL}/${detailId}/is-commission-data-available`)
  },
  generateCommissionData: id => {
    return axios.get(`${BASE_URL}/generate-commission-data/${id}`)
  },
  getCommissionData: (policyId) => {
    return axios.get(`${BASE_URL}/get-commission-data/${policyId}`)
  },
  listBusinessTypes: () => {
    return axios.get(`${BASE_URL}/list-business-types`)
  },
  listPolicyTypes: () => {
    return axios.get(`${BASE_URL}/list-policy-types`)
  },
  checkIfShareUnderLimit: (policyId) => {
    return axios.get(`${BASE_URL}/check-if-share-under-limit/${policyId}`)
  },
  generateReinsuranceShare: (policyId) => {
    return axios.get(`${BASE_URL}/generate-reinsurance-share/${policyId}`)
  },
  generateReinsuranceData: (policyId) => {
    return axios.get(`${BASE_URL}/generate-reinsurance-data/${policyId}`)
  },
  getReinsuranceData: (id) => {
    return axios.get(`${BASE_URL}/get-reinsurance-data/${id}`)
  },
  listParticipants: () => {
    return axios.get(`${BASE_URL}/list-treaty-codes`)
  },
  listPartnerGroups: () => {
    return axios.get(`${BASE_URL}/list-partner-groups`)
  },
  isPolicyReinsuranceCompleted: (id) => {
    return axios.get(`${BASE_URL}/is-policy-reinsurance-completed/${id}`)
  },
  isPolicyConfigurationCompleted: (id) => {
    return axios.get(`${BASE_URL}/is-policy-configuration-completed/${id}`)
  }
}
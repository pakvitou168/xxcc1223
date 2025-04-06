const BASE_URL = '/travel/policies/endorsement-services'

export default {
  isCommissionDataAvailable: (detailId) => {
    return axios.get(`${BASE_URL}/${detailId}/is-commission-data-available`)
  },
  getCommissionData:id =>{
    return axios.get(`${BASE_URL}/${id}/get-commission-data`)
  },
  getReinsuranceData:(id)=>{
    return axios.get(`${BASE_URL}/${id}/get-reinsurance-data`);
  },
  showPolicyCancellationTab:(id)=>{
    return axios.get(`${BASE_URL}/${id}/show-policy-cancellation-tab`);
  },
  listRefundTypeOptions: (id) => {
    return axios.get(`${BASE_URL}/list-refund-type-options/${id}`);
  },
}
import axios from "axios";

const BASE_URL = '/hs/endorsements'

export default {
  detail: (id) => {
    return axios.get(`${BASE_URL}/${id}`)
  },
  generate: (form, id) => {
    return axios.post(`${BASE_URL}/${id}/generate`, form);
  },
  listEndorsementTypes: () => {
    return axios.get(`${BASE_URL}/list-endorsement-types`);
  },
  getValidPeriod: policyId => {
    return axios.get(`${BASE_URL}/${policyId}/valid-period`);
  },
  canGenerate: policyId => {
    return axios.get(`${BASE_URL}/${policyId}/can-generate`);
  },
  approve: (form, id) => {
    return axios.post(`${BASE_URL}/${id}/approve`, form);
  },
  showDetail: id => {
    return axios.get(`${BASE_URL}/${id}/show-detail`);
  },
  getPremium: (id, rawNumber) => {
    return axios.get(`${BASE_URL}/${id}/get-premium/${rawNumber}`);
  },
  save: (form, id) => {
    return axios.put(`${BASE_URL}/${id}`, form)
  },
  saveCancellation:(id,form)=>{
    return axios.put(`${BASE_URL}/${id}/save-cancel-policy-endorsement`,form)
  },
  update:(form,id)=>{
    return axios.put(`${BASE_URL}/${id}`,form)
  },
  config: (form, id) => {
    return axios.patch(`${BASE_URL}/${id}/config`, form)
  },
  importData: (form, id) => {
    return axios.post(`${BASE_URL}/${id}/import-endorsement`, form, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
  }
}
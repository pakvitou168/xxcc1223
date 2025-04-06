import axios from "axios";
import {
  transformClaimRegisterForDisplay,
  transformSchemaForDisplay,
  prepareSchemaPayload
} from './date.js';

const RESOURCE = '/hs/claim-service'

export default {
  save(form, method) {
    let payload = prepareSchemaPayload(form);
    if (method === 'POST')
      return axios.post(`${RESOURCE}/registers`, payload);
    else {
      return axios.put(`${RESOURCE}/registers/${form.id}`, payload);
    }
  },
  getData(id) {
    return axios.get(`${RESOURCE}/registers/${id}/edit`)
      .then(response => {
        return transformClaimRegisterForDisplay(response);
      });
  },
  delete: id => {
    return axios.delete(`${RESOURCE}/registers/${id}`)
  },
  approve: (form, id) => {
    return axios.post(`${RESOURCE}/registers/${id}/approve`, form)
  },
  detail: id => {
    return axios.get(`${RESOURCE}/registers/${id}`)
  },
  getLovs: search => {
    return axios.get(`${RESOURCE}/get-lovs`, {
      params: {
        search: search
      }
    });
  },
  filterPolicy: search => {
    return axios.get(`${RESOURCE}/filter-claimable-policies`, {
      params: {
        search: search
      }
    });
  },
  getInsuredPersons(policyId, search) {
    return axios.get(`${RESOURCE}/get-insured-persons`, {
      params: {
        search: search,
        policy_id: policyId
      }
    });
  },
  getCauseOfLoss(policyId, insuredId) {
    return axios.get(`${RESOURCE}/get-cause-of-loss/${policyId}/${insuredId}`);
  },
  printUrl(claimId, lang, letterHead) {
    return `${RESOURCE}/register-pdf/${claimId}/${lang}/${letterHead}`;
  },
  printSchemaUrl(claimId, lang, letterHead) {
    return `${RESOURCE}/register-schema-pdf/${claimId}/${lang}/${letterHead}`;
  },
  getSchema(claimId) {
    return axios.get(`${RESOURCE}/get-shema-data/${claimId}`)
      .then(response => {
        return transformSchemaForDisplay(response);
      });
  },
  saveSchema(id, data) {
    let payload = prepareSchemaPayload(data);

    return axios.post(`${RESOURCE}/save-schema-data/${id}`, payload);
  },
  approveSchema(id, data) {
    return axios.post(`${RESOURCE}/approve-schema-data/${id}`, data);
  },
  revise: (id, data) => {
    return axios.post(`${RESOURCE}/schema-revise/${id}`, data);
  },
}
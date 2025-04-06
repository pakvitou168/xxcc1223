import axios from "axios";

const serviceUrl = '/pa/services';
export default {
    selection: () => {
        return axios.get(`${serviceUrl}/selections`)
    },
    searchInsuredPs: (form) => {
        return axios.post(`${serviceUrl}/search-insured-persons`, form)
    },
    validateFileUpload: (form) => {
        return axios.post(`${serviceUrl}/validate-file`, form)
    },
    filterBusiness: (businessChannel) => {
        return axios.get(`${serviceUrl}/business-options/${businessChannel}`)
    },
    classes: () => {
        return axios.get(`${serviceUrl}/classes`)
    },
    optionalExt: (id) => {
        return axios.get(`${serviceUrl}/${id}/optional-extensions`)
    },
    optionalExtBase: () => {
        return axios.get(`${serviceUrl}/option-extensions-base`)
    },
    updateOptExt: (data, id) => {
        return axios.post(`${serviceUrl}/${id}/update-optional-extensions`, data)
    },
    businessInfo: (bcode) => {
        return axios.get(`${serviceUrl}/${bcode}/business-info`)
    },
    policyConfig: () => {
        return axios.get(`${serviceUrl}/policy-config`)
    },
    reinsuranceSelection: (params) => {
        return axios.get(`${serviceUrl}/reinsurance-selection`, { params: params });
    },
    endorsementTypes:() => {
        return axios.get(`${serviceUrl}/endorsement-types`);
    }
}
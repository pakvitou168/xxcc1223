const RESOURCE = '/travel/policies/quotations'

export default {
  create: form => {
    return axios.post(`${RESOURCE}`, form);
  },
  getLovs: () => {
    return axios.get(`${RESOURCE}/get-lovs`);
  },
  getBusinessChannelsLov: saleChannelId => {
    return axios.get(`${RESOURCE}/get-business-channels-lov/${saleChannelId}`);
  },
  getCustomersLov: customerType => {
    return axios.get(`${RESOURCE}/get-customers-lov/${customerType}`);
  },
  detail: id => {
    return axios.get(`${RESOURCE}/${id}`)
  },
  approve: (form, id) => {
    return axios.post(`${RESOURCE}/${id}/approve`, form)
  },
  accept: (form, id) => {
    return axios.post(`${RESOURCE}/${id}/accept`, form)
  },
  getProductCodeByUploadExcel: (form) => {
    return axios.post(`${RESOURCE}/get-product-code-by-upload-excel`, form)
  },
  getProductCodeFromUploadExcel: (form) => {
    return axios.post(`${RESOURCE}/get-product-code-from-upload-excel`, form,{
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json',
          }
        });
  },
}
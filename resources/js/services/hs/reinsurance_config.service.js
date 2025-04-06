const BASE_URL = '/hs/reinsurance-config'

export default {
    listDefaultPartnerGroups: (productCode) => {
        return axios.get(`${BASE_URL}/get-default-reinsurance-config/${productCode}`)
    },
}
import axios from "axios"

const BASE_URL = '/api/sm/users';
export default {
    save: async(form) => {
        return axios.post(`${BASE_URL}`,form)
    },
    detail: async(id) => {
        return axios.get(`${BASE_URL}/${id}`)
    },
    update: async(form,id) => {
        return axios.patch(`${BASE_URL}/${id}`,form)
    },
    listGroups: () => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-group`,
            method: 'GET',
        })
    },

    listRoles: () => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-role`,
            method: 'GET',
        })
    },

    listPermissions: () => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-permission`,
            method: 'GET',
        })
    },

    listOrganizations: () => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-organization`,
            method: 'GET',
        })
    },

    listAuthenticators: () => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-authenticator`,
            method: 'GET'
        })
    },

    listBranchesByOrg: (params) => {
        return axios({
            baseURL: BASE_URL,
            url: `/list-branch-by-org`,
            method: 'GET',
            params: {
                org_code: 'PGI',
            }
        })
    },
}
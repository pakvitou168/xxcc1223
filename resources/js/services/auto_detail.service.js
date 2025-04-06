const RESOURCE = '/auto-details'

export default {
    save: (form, method) => {
        if (method === 'POST')
            return axios.post(`${RESOURCE}`, form);
        else if (method === 'PUT') {
            return axios.put(`${RESOURCE}/${form.id}`, form);
        }
    },

    updateGeneralEndorsement: (form) => {
        return axios.put(`${RESOURCE}/update-general-endorsement/${form.id}`, form);
    },
}

const RESOURCE = '/hs'

export default {
    updateIssueDate: dataId => {
    return axios.put(`${RESOURCE}/update-issue-date/${dataId}`);
  },
}
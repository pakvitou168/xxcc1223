const RESOURCE = '/travel/policies'

export default {
    updateIssueDate: dataId => {
    return axios.put(`${RESOURCE}/update-issue-date/${dataId}`);
  },
}
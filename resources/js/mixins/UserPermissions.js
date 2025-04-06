export default {
    data() {
        return {
            authorizedFunctions: JSON.parse(document.querySelector('#authorized-functions').value),
        }
    },
    computed: {
        getFunctions() {
            return this.authorizedFunctions.filter(item => item.code === this.functionCode)
        },
        hasCode() {
            return this.getFunctions.length > 0
        },
        canView() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.VIEW'}`);
        },
        canAddNew() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.NEW'}`);
        },
        canUpdate() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.UPD'}`);
        },
        canDelete() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.DEL'}`);
        },
        canApprove() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.APV'}`);
        },
        canAccept() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.ACP'}`);
        },
        canUpload() {
            return this.authorizedFunctions.some(item => item.code === `${this.functionCode+'.UPL'}`);
        }
    },
    methods: {
        getFunctionsByCode(code) {
            return this.authorizedFunctions.filter(item => item.code === code)
        },
        hasPermissionCode(code) {
            return this.getFunctionsByCode(code).length > 0
        },
        canViewByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.VIEW'}`);
        },
        canAddNewByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.NEW'}`);
        },
        canUpdateByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.UPDATE'}`);
        },
        canDeleteByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.DELETE'}`);
        },
        canApproveByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.APPROVE'}`);
        },
        canAcceptByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.ACCEPT'}`);
        },
        canUploadByCode(code) {
            return this.authorizedFunctions.some(item => item.code === `${code+'.UPLOAD'}`);
        }
    }
}

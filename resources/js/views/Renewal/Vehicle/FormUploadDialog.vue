<template>
    <Dialog
        header="Upload Vehicles"
        ref="dl"
        class="p-fluid w-6/12 custom-dialog z-10"
        position="top"
        :visible="isVisible"
        :modal="true"
        :closable="false"
        :draggable="false"
    >
        <div>
            <!-- <FormulateForm v-model="formValues">
                <div class="sm:grid-cols-1 gap-10">
                    <FormulateInput
                        type="file"
                        name="file"
                        help="Select excel file"
                        accept=".xlsm"
                        @change="uploadFile"
                    />
                </div>
                <div class="vehicle-upload-msg break-words"></div>
                <ProgressSpinner v-if="requireSpinning" style="width:50px;height:50px" strokeWidth="8" animationDuration=".5s"/>
            </FormulateForm> -->
        </div>
         <p class="mt-2" style="color:red"><span class="font-bold">Note:</span> Support upload max 1,000 vehicles</p>
        <template #footer>
            <div class="flex w-full">
                <p class="text-lg font-medium mr-auto"></p>
                <Button label="Cancel" class="p-button-danger p-button-text" @click="hideDialog"/>
                <Button label="Submit" :class="{'opacity-50 cursor-not-allowed' : isDisabledSubmitButton}" :disabled="isDisabledSubmitButton" autofocus @click="saveAndClose" />
            </div>
        </template>
    </Dialog>
</template>

<script>

    import readXlsxFile from 'read-excel-file';

    export default {
        props: {
            isVisible: Boolean,
            submitted: Boolean,
            productCode: String,
            masterDataId: [Number, String],
            quotationDocumentNo: String,
        },

        data() {
            return {
                formValues: {},
                vehicleUsageOptions: {},
                makeList: {},
                modelList: {},
                vehicleUploadErrorIndexes: [],
                isDisabledSubmitButton: true,
                isUploadInProgress: false,
            }
        },

        computed: {
            requireSpinning(){
                return this.isUploadInProgress
            }
        },

        methods: {
            hideDialog() {
                this.finishUploading()
                this.$emit('hideUploadDialog')
            },

            uploadFile(e) {
                let vehicleUploadMsg = document.querySelector('.vehicle-upload-msg')
                vehicleUploadMsg.innerHTML = '<b>Uploading</b>';
                var file = e.target.files ? e.target.files[0] : null;
                this.vehicleUploadErrorIndexes = []
                this.startUploading()
                readXlsxFile(file,{ sheet: 1 }).then((rows) => {
                        rows.shift()
                        this.getVehicleMake(rows).finally(()=>{
                            this.getVehicleModel(rows).finally(()=>{
                                this.checkVehicleError(rows).finally(()=>{
                                    if(this.vehicleUploadErrorIndexes.length > 0){
                                        this.vehicleUploadErrorIndexes.forEach((num, index) => {
                                            this.vehicleUploadErrorIndexes[index] = num + 2;
                                        });
                                        let vehicleUploadMsg = document.querySelector('.vehicle-upload-msg')
                                        vehicleUploadMsg.innerHTML = '<b>Records are not matched.</br>Please check these vehicle indexes in your excel file: </br> <p style="color:red">[' + this.vehicleUploadErrorIndexes+']</p></b>';
                                        this.finishUploading()
                                    }else{
                                        var formData = new FormData()
                                        formData.append('file', file)
                                        formData.append('productCode', this.productCode)
                                        formData.append('master_data_type', 'RENEWED_POLICY')
                                        formData.append('master_data_id', this.masterDataId)
                                        axios.post('/auto-service/file-vehicle-import', formData).then(response => {
                                            if(response.data.success){
                                                let vehicleUploadMsg = document.querySelector('.vehicle-upload-msg')
                                                vehicleUploadMsg.innerHTML = `<div>
                                                    <p class="font-bold">File Uploaded Successfully</p>
                                                    <p class="font-semibold mt-2">${response.data.count} record(s) uploaded</p>
                                                </div>`;

                                                this.isDisabledSubmitButton = false
                                                this.finishUploading()
                                            }
                                        }).catch((error)=>{
                                            console.log(error);
                                        })
                                    }
                                })
                            });
                        })
                    })
            },

            async getVehicleMake(rows) {
                try{
                    await axios({
                        method: "post",
                        url: '/auto-service/vehicle-upload/list-makes-by-product/' + this.productCode,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: {
                            rows: rows,
                        },
                    })
                    .then((response) => {
                        this.makeList = response.data;
                    })
                    .catch(function (response) {
                        console.log(response);
                    });
                } catch(err){
                    console.error(err);
                }
            },

            async getVehicleModel(rows) {
                try{
                    await axios({
                        method: "post",
                        url: '/auto-service/vehicle-upload/list-models-by-product-and-make/' + this.productCode,
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        data: {
                            rows: rows,
                            makeList: this.makeList
                        },
                    })
                    .then((response) => {
                        this.modelList = response.data
                    })
                    .catch(function (response) {
                        console.log(response);
                    });
                } catch(err){
                    console.error(err);
                }
            },

            async checkVehicleError(rows){
                rows.map((item, index) => {
                    var makeId = null;
                    var modelId = null;
                    var vehicleUsage = null;
                    var covers = item[12];
                    for(const key in this.makeList){
                        if(item[0] == this.makeList[key].label){
                            makeId = this.makeList[key].value;
                            break;
                        }
                    }
                    for(const key in this.modelList){
                        if(item[1] == this.modelList[key].label && makeId == this.modelList[key].makeId){
                            modelId = this.modelList[key].value;
                            break;
                        }
                    }
                    for(let key in this.vehicleUsageOptions) {
                        if(item[13] == this.vehicleUsageOptions[key]){
                            vehicleUsage = this.vehicleUsageOptions[key];
                            break;
                        }
                    }
                    if(!(makeId && modelId && covers && vehicleUsage))
                        this.vehicleUploadErrorIndexes.push(index);
                })
            },

            handleSubmit() {
                let vehicleUploadMsg = document.querySelector('.vehicle-upload-msg')
                vehicleUploadMsg.innerHTML = '<b>Saving Vehicles</b>';
                this.startUploading()
                return axios.post('/auto-service/clone-auto-temp-to-auto-detail',{
                    'master_data_id': this.masterDataId,
                    'productCode': this.productCode
                })
            },

            handleDeleteVehicleUpload(){
                axios.delete('/auto-service/delete-auto-temp-from-detail',
                    {
                        data: { 'master_data_id': this.masterDataId }
                    }
                ).catch((error)=>{
                    console.log(error);
                })
            },

            handleDeductible() {
                var formData = {
                    data_id: this.masterDataId,
                    product_code: this.productCode,
                    request_type: 'POLICY',
                }

                axios.post('/autos/functions/generate-overall-deductible', formData)
                    .then(response => {
                        if (response.data[0].code === 'SUC') {
                            this.$emit('toastDeductibleMessage')
                        }
                    })
                    .catch(error => console.log(error))
            },

            handlePremium() {
                let vehicleUploadMsg = document.querySelector('.vehicle-upload-msg')
                vehicleUploadMsg.innerHTML = '<b>Calculating Total Premium</b>';
                var formData = {
                    data_id: this.masterDataId,
                    product_code: this.productCode,
                    request_type: 'RENEWED_POLICY',
                }
                axios.post('/autos/functions/generate-overall-premium', formData)
                .then(response => {
                    if (response.data[0].stat === 'SUC'){
                        this.$emit('updateTotalPremium', response.data[0].total_premium)
                        this.$emit('vehicleListUpdated')
                        this.hideDialog()
                        this.$emit('finishCalculatingPremium')
                        // Only after the total premium is generated, then it is possible to call a function to generate quotation number
                        if(!this.quotationDocumentNo)
                            this.$emit('handleQuotationNumGeneration')
                    }
                })
            },

            checkValidation(){
                var formData = {
                    id: null,
                    data_id: this.masterDataId,
                    detail_id: null,
                    product_line: 'AUTO',
                    product_code: this.productCode,
                    request_type: 'RENEWED_POLICY',
                    group_type: this.quotationDocumentNo ? 'check_regen_premium' : 'gen_quote',
                    p_type: ''
                }

                return axios.get('/autos/functions/check-validation', {params : formData})
            },

            saveAndClose() {
                this.isDisabledSubmitButton = true
                this.handleSubmit().then(response => {
                    if (response.data.success) {
                        this.toastMessage(response.data.message, 'Success')
                        this.checkValidation().then((responseValidation) =>{
                            if(responseValidation.data[0].is_checked == 'Y'){
                                this.handleDeductible()
                                this.$emit('startCalculatingPremium')
                                this.handlePremium()
                            } else {
                                this.toastMessage(responseValidation.data[0].message, 'Error')
                            }
                        })
                    }
                })
            },

            listVehicleUsages() {
                axios.get(`/auto-service/list-vehicle-usage-by-product-code/${this.productCode}`).then(response => {
                    this.vehicleUsageOptions = response.data
                })
            },

            startUploading(){
                this.isUploadInProgress = true
            },

            finishUploading(){
                this.isUploadInProgress = false
            },

            toastMessage(msg, type, position = 'bottom') {
                this.$notify({
                    group: position,
                    title: type,
                    text: msg
                }, 4000);
            },
        },

        mounted(){
            this.listVehicleUsages()
        }
    }
</script>

<template>
    <div>
        <div class="intro-y box overflow-hidden mt-5">
            <div class="text-center">
                <div class="pt-10">
                    <div class="text-theme-1 font-semibold text-3xl text-center uppercase">{{product_insurance_name}}</div>
                    <div class="mt-2 text-xl text-center uppercase"><span>ENDORSEMENT</span></div>
                </div>
                <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
                    <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
                        <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">Policy & Endorsement No.: <span>{{ documentNo }}</span></div>
                        <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">Business Code: {{ formValues.business_code }}</div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5">
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">THE INSURED NAME:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-bold mb-3"><span>{{ formValues.insured_name }}</span></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">CORRESPONDENCE ADDRESS:</div>
                    </div>
                    <div class="w-2/3">
                        <!-- <div class="text-md font-medium mb-3">No. 27DEF, Preah Monivong Blvd, Sangkat Srah Chork, Khan Daun Penh, Phnom Penh, Kingdom of Cambodia.</div> -->
                        <div class="text-md font-medium mb-3"><span>{{customer_address}}</span></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ effectivePeriod }} - {{ periodOfInsurance }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ENDORSEMENT EFFECTIVE DATE:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ endorsementEffectiveDate }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">TYPE OF ENDORSEMENT:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm">{{ endorsementType }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-sm font-bold mb-3">ENDORSEMENT DESCRIPTION:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-sm mb-3">It is hereby understood and agreed that the amendment is endorsed to the above mentioned Policy as:</div>
                        <div class="text-sm mb-3" v-html="endorsementDesc"></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3"></div>
                    <div class="w-2/3">
                        <div class="text-sm mb-3">Subject otherwise to terms, conditions and exclusions of the Policy.</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ENDORSEMENT PREMIUM (USD):</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ endorsementPremium }}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ISSUED ON:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{issuedDate}}</div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-1/3">
                        <div class="text-md font-bold mb-3">ISSUED BY:</div>
                    </div>
                    <div class="w-2/3">
                        <div class="text-md font-medium mb-3">{{ formValues.issued_by }}</div>
                    </div>
                </div>
            </div>
            <div class="px-5 sm:px-16 py-5">
                <div class="flex">
                    <div class="w-auto">
                        <div class="text-md font-bold mb-3 uppercase">Phillip General Insurance (Cambodia) Plc.</div>
                        <div class="my-2" v-bind:class="{ 'relative': signature }" style="min-height: 150px;">
                            <img v-if="signature && canShowSignature" class="img-over" :src="'/' + signature.file_url" style="max-height: 150px">
                            <img v-if="signature && canShowSignature" class="img-under" src="/images/stamp/phillip_insurance.png" style="max-height: 150px">
                        </div>

                        <hr class="my-3">

                        <div class="text-md mb-3 font-medium">Authorised Signature</div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="hasCommissionDataYet" class="intro-y box overflow-hidden mt-5">
            <div class="text-center">
                <div class="pt-10">
                <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
                    Commission
                </div>
                </div>
            </div>
            <div v-for="(commission, index) in commissionData" :key="index" class="pt-5 pb-2 first:pt-0 px-5 sm:px-16 py-5">
                    <ComissionItem
                        :id="id"
                        :index="index"
                        :commission="commission"
                        :dataId="dataId"
                        @toggle='toggleCommission'
                        @collapse='collapseCommission'
                        :businessName="businessName"/>
            </div>
        </div>
        <div v-if='hasReinsuranceDataYet' class="intro-y box overflow-hidden mt-5">
            <div class="text-center">
                <div class="pt-10">
                    <div class="text-theme-1 font-semibold text-3xl text-center uppercase">REINSURANCE</div>
                </div>
            </div>
            <div v-for="(reinsurance, detailId, index) in reinsuranceData" :key="detailId" class="pt-5 pb-2 first:pt-0 px-5 sm:px-16 py-5">
                <ReinsuranceItem
                    v-if="id"
                    :id="id"
                    :detailId="detailId"
                    :index="index"
                    :reinsurance="reinsurance"
                    :readOnly="true"
                    :isEndorsement="true"
                    :endorsementNo="documentNo"
                    @toggle='toggleReinsurance'
                    @collapse='collapseReinsurance'
                    />
            </div>
        </div>
    </div>
</template>

<script>

import ReinsuranceItem from '../../../../Policy/FormTabs/Reinsurance/ReinsuranceItem.vue';
import ComissionItem from '../../Commission/ComissionItem.vue';

export default {
    components:{
        ReinsuranceItem,
        ComissionItem
    },
    props: {
        id: [Number, String],
        documentNo: String,
        endorsementId: [Number, String],
        endorsementStatus: String,
    },

    data() {
        return {
            functionCode: 'AUTO',
            businessName: '',
            formValues: {},
            signature: null,
            endorsementType: '',
            endorsementDesc: '',
            endorsementPremium: '',
            reinsuranceData: {},
            commissionData: null,
        }
    },

    computed: {
        customer_address() {
            return this.formValues.customer ? this.handleCustomerAddress(this.formValues.customer.address_en, this.formValues.customer.village_en, this.formValues.addressData, this.formValues.country) : null
        },
        product_insurance_name(){
            return this.formValues.product ? this.formValues.product.name : null
        },
        issuedDate() {
            var date = this.formValues.updated_at ?? this.formValues.created_at
            return moment(date).format('DD/MM/YYYY')
        },
        periodOfInsurance() {
            if (this.formValues.effective_date_from && this.formValues.effective_date_to) {
                return `From ${moment(this.formValues.effective_date_from).format('DD/MM/YYYY')} To ${ moment(this.formValues.effective_date_to).format('DD/MM/YYYY') } (Both days inclusive)`
            }
            return ''
        },
        endorsementEffectiveDate() {
            return moment(this.formValues.endorsement_e_date).format('DD/MM/YYYY')
        },
        hasCommissionDataYet(){
            return !_.isEmpty(this.commissionData)
        },
        hasReinsuranceDataYet(){
            return Object.keys(this.reinsuranceData).length !== 0
        },
        canShowSignature() {
            // If endorsement is approved
            return this.endorsementStatus === 'APV'
        },
        effectivePeriod(){
            return this.formValues.effective_day + ' days'
        },
        dataId() {
            return this.commissionData[0].data_id
        },
    },

    methods: {
        getEndorsement() {
            if (this.id) {
                axios.get('/api/endorsements/show-detail/' + this.id).then(response => {
                    if (response) {
                        this.formValues = response.data.auto
                        this.endorsementType = response.data.endorsement_type
                        this.endorsementDesc = response.data.endorsement_description
                        if(this.formValues.quotation)
                            this.signature = response.data.signature
                        else{
                            axios.get('/policy-service/get-signature/' + this.endorsementId).then(response => {
                                if (response)
                                    this.signature = response.data.signature
                            });
                        }

                    }
                })
                .then(() => this.calculateEndorsementPremium(this.documentNo))
                .then(() => this.getRawEndorsementPremium(this.documentNo))
                .finally(()=>{
                    this.$emit('get-vehicles-length', this.formValues.vehicles.length);
                })
            }
        },

        calculateEndorsementPremium(documentNo) {
            axios.get(`/api/endorsements/get-premium/${this.id}/${documentNo}`).then(response => {
                this.endorsementPremium = response.data
            })
        },
        getRawEndorsementPremium(documentNo) {
            axios.get(`/api/endorsements/get-premium/${this.id}/${documentNo}/1`).then(response => {
                this.$emit('get-total-premium', response.data);
            })
        },
        getReinsuranceData() {
            if (this.id)
                axios.get(`/api/endorsements/get-reinsurance-data/${this.id}`).then(response => this.reinsuranceData = response.data)
        },
        getCommissionData() {
            if (this.id)
                axios.get(`/endorsement-service/get-commission-data/${this.id}`)
                .then((response) => (this.commissionData = response.data))
                .then(() => {
                    if(!_.isEmpty(this.commissionData))
                        this.getBusinessNameByBusinessCode(this.commissionData[0].business_code)
                })
        },
        getBusinessNameByBusinessCode(businessCode) {
            axios.get(`/policy-service/get-business-name-by-business-code/${businessCode}`).then(response => {
                this.businessName = response.data
            })
        },

        handleCustomerAddress(customAddress, village, address, country){
            if(address)
                if(country)
                    return `${customAddress ? customAddress + ', ' : ''}
                            ${village ? village + ', ' : ''}
                            ${address.commune ? address.commune + ', ' : ''}
                            ${address.district ? address.district + ', ' : ''}
                            ${address.province ? address.province : ''}
                            ${address.province == 'Phnom Penh' ? ' Capital, ' : ' Province, '}
                            ${country}`
                else
                    return `${customAddress ? customAddress + ', ' : ''}
                            ${village ? village + ', ' : ''}
                            ${address.commune ? address.commune + ', ' : ''}
                            ${address.district ? address.district + ', ' : ''}
                            ${address.province ? address.province : ''}
                            ${address.province == 'Phnom Penh' ? ' Capital' : ' Province'}`
            else if(country)
                return `${customAddress ? customAddress + ', ' : ''}
                        ${village ? village + ', ' : ''}
                        ${country}`
            else if(village)
                return `${customAddress ? customAddress + ', ' : ''}
                        ${village}`
            else
                    return `${customAddress}`
        },

        toggleCommission(vehicleNo) {
            for (let index = 0; index < _.size(this.commissionData); index++){
                var header = document.querySelector('.show-commission-' + index)
                var icon = document.querySelector('.commission-icon-' + index)
                if(vehicleNo !== index){
                    header.classList.add('collapse')
                    icon.classList.add('rotate', 'icon-color')
                    continue;
                }
                header.classList.toggle('collapse')
                icon.classList.toggle('rotate')
                icon.classList.toggle('icon-color')
            }
        },

        collapseCommission(vehicleNo){
            var classId = document.querySelector('.show-commission-' + vehicleNo)
            var iconId = document.querySelector('.commission-icon-' + vehicleNo)
            classId.classList.remove('collapse')
            iconId.classList.remove('rotate')
            iconId.classList.remove('icon-color')
        },

        toggleReinsurance(vehicleNo) {
            for (let index = 0; index < _.size(this.reinsuranceData); index++){
                var header = document.querySelector('.show-reinsurance-' + index)
                var icon = document.querySelector('.reinsurance-icon-' + index)
                if(vehicleNo !== index){
                    header.classList.add('collapse')
                    icon.classList.add('rotate', 'icon-color')
                    continue;
                }
                header.classList.toggle('collapse')
                icon.classList.toggle('rotate')
                icon.classList.toggle('icon-color')
            }
        },

        collapseReinsurance(vehicleNo){
            var classId = document.querySelector('.show-reinsurance-' + vehicleNo)
            var iconId = document.querySelector('.reinsurance-icon-' + vehicleNo)
            classId.classList.remove('collapse')
            iconId.classList.remove('rotate')
            iconId.classList.remove('icon-color')
        },
    },

    mounted() {
        this.getEndorsement()
        this.getReinsuranceData();
        this.getCommissionData();
    }
}
</script>

<style scoped>
    .img-under {
        position: absolute;
        left: 0px;
        top: 0px;
        z-index: -1;
    }
    .img-over {
        position: absolute;
        left: 80px;
        top: 10px;
        z-index: -1;
    }
</style>

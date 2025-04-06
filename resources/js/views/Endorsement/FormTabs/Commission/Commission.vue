<template>
    <div>
        <div v-for="(commission, index) in formValues" :key="index" class="pt-5 pb-2 border-b border-gray-300 first:pt-0 last:border-b-0">
            <ComissionItem
                :id="id"
                :index="index"
                :commission="commission"
                :dataId="dataId"
                :canSave="true"
                @toggle='toggle'
                @collapse='collapse'
                :businessName="businessName"/>
        </div>
    </div>
</template>

<script>

import ComissionItem from './ComissionItem.vue'

export default {
    props: {
        id: [Number, String],
        dataId: [Number, String],
        cancelRoute: String,
    },

    components: {
        ComissionItem,
    },

    data() {
        return {
            formValues: {},
            businessName: '',
        }
    },

    methods: {
        checkCommissionDataAvailability(){
            axios.get(`/endorsement-service/is-commission-data-available/${this.id}`)
                .then(response => {
                    if(response.data)
                        this.getCommissionData()
                    else
                        this.generateCommissionData()
                })
                .catch(err => console.log(err))
        },
        generateCommissionData() {
            axios.get(`/policy-service/generate-commission-data/${this.id}`)
                .then(() => this.getCommissionData())
                .catch(err => console.log(err))
        },

        getCommissionData() {
            if (this.id)
                axios.get(`/endorsement-service/get-commission-data/${this.id}`)
                    .then(response => this.formValues = response.data)
                    .then(() => {
                        if(!_.isEmpty(this.formValues))
                            this.getBusinessNameByBusinessCode(this.formValues[0].business_code)
                    })
        },

        getBusinessNameByBusinessCode(businessCode) {
            axios.get(`/policy-service/get-business-name-by-business-code/${businessCode}`).then(response => {
                this.businessName = response.data
            })
        },

        toggle(vehicleNo) {
            for (let index = 0; index < _.size(this.formValues); index++){
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

        collapse(vehicleNo){
            var classId = document.querySelector('.show-commission-' + vehicleNo)
            var iconId = document.querySelector('.commission-icon-' + vehicleNo)
            classId.classList.remove('collapse')
            iconId.classList.remove('rotate')
            iconId.classList.remove('icon-color')
        },

        toastMessage(msg, type, position = 'bottom') {
            this.$notify({
                group: position,
                title: type,
                text: msg
            }, 4000);
        }
    },

    mounted() {
        this.checkCommissionDataAvailability()
    },
}
</script>

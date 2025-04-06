<template>
    <div>
        <div class="intro-y box p-5 mt-5">

            <div class="intro-y flex mb-4 p-1">
                <h2 class="text-xl font-medium mr-auto">Exchange Rate  Detail</h2>
                <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve" @click="openApproveDialog()">
                    <span class="h-6 leading-6">Approval</span>
                </button>
                <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                <router-link v-if="canUpdate" :to="{ name: 'ExchangeRateEdit', params: { id: id }}" class="btn btn-primary mx-1 intro-x">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </router-link>
            </div>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Branch</span>
                <p class="text-base text-bold intro-x">{{ branchName }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Rate Date</span>resources/js/router/router.js
                <p class="text-base text-bold intro-x">{{ formValues.rate_date }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">From Currency</span>
                <p class="text-base text-bold intro-x">{{ formValues.ccy1 }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">To Currency</span>
                <p class="text-base text-bold intro-x">{{ formValues.ccy2 }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Rate Type</span>
                <p class="text-base text-bold intro-x">{{ formValues.rate_type }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Buy Rate</span>
                <p class="text-base text-bold intro-x">{{ formValues.buy_rate }}</p>
            </div><hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Sale Rate</span>
                <p class="text-base text-bold intro-x">{{ formValues.sale_rate }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Middle Rate</span>
                <p class="text-base text-bold intro-x">{{ formValues.mid_rate }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Buy Spread</span>
                <p class="text-base text-bold intro-x">{{ formValues.buy_spread }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Sale Spread</span>
                <p class="text-base text-bold intro-x">{{ formValues.sale_spread }}</p>
            </div>
            <hr>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Rate Serial</span>
                <p class="text-base text-bold intro-x">{{ formValues.rate_serial }}</p>
            </div>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Status</span>
                <p class="text-base text-bold intro-x">{{ formValues.status }}</p>
            </div>
            <div class="flex m-2">
                <span class="text-base w-1/4 intro-x">Created By</span>
                <p class="text-base text-bold intro-x">{{ formValues.created_by }}</p>
            </div>
            <div class="text-right mt-5">
                <router-link :to="{ name: 'ExchangeRate' }" class="btn btn-primary w-24 mr-1" tag="button">Back</router-link>
            </div>
        </div>
        <ApprovelDialog
            :isVisible="approveDialog"
            header="Approval"
            :submitted="submitted"
            :options="[{ label: 'Accept', value: 'ACT' }, { label: 'Reject', value: 'REJ' }]"
            value="ACT"
            @hideDialog="hideDialog"
            @confirm="approve"
            :loading="loading"
        />
    </div>
</template>

<script>

import UserPermissions from '../../../mixins/UserPermissions';
import ApprovelDialog from './ApprovalDialog.vue';
import eventBus from '../../../eventBus'

export default {
    mixins: [UserPermissions],
    components: {
        ApprovelDialog,
    },
    data() {
        return{
            id: this.$route.params.id,
            functionCode: 'EXCHANGE_RATE',
            formValues: {},
            approveDialog: false,
            submitted: false,
            loading: false,
        }
    },

    computed: {
        canApproveCond() {
            // If don't have permission to approve
            if (!this.canApproveByCode('EXCHANGE_RATE')) return false
            if (!this.formValues.id) return false
            // If not yet approved
            return this.formValues.status === 'PND' ? true : false;
        },
        branchName() {
            return this.formValues.branch?.name
        },
        canUpdate() {
          if (this.formValues.status !== 'PND') return false;
          return this.canUpdateByCode('EXCHANGE_RATE')
        },
    },

    methods: {
        getExchangeRate() {
            if (this.id) {
                axios.get(`/exchange-rates/${this.id}/`)
                .then(response => {
                    this.formValues = response.data
                    if(response.data?.error)
                        notify(response.data.message, 'error','bottom-right');
                })
            }
        },
        openApproveDialog() {
            this.approveDialog = true
            this.submitted = false
        },
        hideDialog() {
            this.approveDialog = false
            this.submitted = false
        },
        approve(form) {

            this.submitted = true

            if (form.status) {
              console.log('Submitting approval...');

              axios.post(`/exchange-rate-service/approve/${this.id}`, {
                    approved_status: form.status,
                }).then(response => {
                    if (response.data.success) {
                        console.log('Approval successful, emitting exchangeRateUpdated');
                        eventBus.$emit('exchangeRateUpdated')
                        notify(response.data.message, 'success','bottom-right')
                        this.$router.push({
                            name:"ExchangeRate"
                        });
                    }
                }).catch(err => {
                  console.error('Approval error:', err);
                  notify('Error', 'error','bottom-right')
              })
            }
        },
        handleDelete(id) {
            console.log('Submitting handleDelete...');
            this.$confirm.require({
                message: 'Do you want to delete this record?',
                header: 'Delete',
                icon: 'pi pi-info-circle',
                acceptClass: "p-button-danger",
                rejectClass: "p-button-secondary p-button-outlined",
                blockScroll: false,
                rejectLabel: 'Cancel',
                acceptLabel: 'Delete',
                accept: () => {
                    axios.delete(`/exchange-rates/${id}`).then(response => {
                        if (response.data.success) {
                            console.log('handleDelete, emitting exchangeRateUpdated');

                            eventBus.$emit('exchangeRateUpdated')
                            notify(response.data.message, 'success','bottom-right')
                           this.$router.push({ name: 'ExchangeRate' });
                        }
                    }).catch(err => {
                        notify('Error', 'error','bottom-right')
                    })
                },
            });
        },
    },

    mounted() {
      console.log('Container mounted, setting up exchangeRateUpdated listener');
      this.getExchangeRate()
    },
}
</script>

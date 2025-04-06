<template>
    <div>
        <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
            <h3 class="text-lg">PA Policy Detail </h3>
            <div class="flex-1 flex justify-end">
                <button v-if="canEndorse"
                    class="px-3 mr-1 bg-orange-500 bg-opacity-90 text-white rounded-md focus:outline-none hover:bg-orange-400"
                    title="Make endorsement" @click="handleEndorse">Endorse
                </button>
                <button v-if="canApprove"
                    class="px-3 mr-1 bg-blue-500 bg-opacity-90 text-white rounded-md focus:outline-none hover:bg-blue-400"
                    title="Approve" @click="handleApprove">Approve
                </button>
                <Button v-if="canEdit"
                    class="px-5 mr-1 bg-blue-800 text-md bg-opacity-90 text-white rounded-md focus:outline-none hover:bg-blue-600"
                    title="Edit" @click="handleEdit" icon="pi pi-pen-to-square">
                </Button>
                <Button v-if="canDelete"
                    class="px-5 mr-1 bg-red-600 bg-opacity-90 text-white rounded-md focus:outline-none hover:bg-red-400"
                    title="Edit" :loading="state.loading" @click="handleDelete" icon="pi pi-trash">
                </Button>
                <button class="mr-1 px-3 py-2 bg-blue-500 text-white rounded-md focus:outline-none hover:bg-blue-400"
                    title="Refresh" @click="refresh"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        :class="{ 'animate-spin': refreshing }" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                    </svg>
                </button>

                <div class="dropdown">
                    <button class="dropdown-toggle button-primary shadow-md" title="Print">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <div class="dropdown-menu w-72">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a v-if="canPrintInvoice"
                                class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithSignature" target="_blank">Invoice (Signature)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printInvoiceUrlWithoutSignature" target="_blank">Invoice (No Signature)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLink" target="_blank">Policy Schedule (Letterhead EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLink" target="_blank">Policy Schedule (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLink" target="_blank">Policy Schedule (No Signature
                                EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLinkKh" target="_blank">Policy Schedule (Letterhead KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLinkKh" target="_blank">Policy Schedule (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">Policy Schedule (No
                                Signature KH)</a>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <div v-if="state.loading" class="w-full box p-10 mt-2">
            <LoadingIndicator />
        </div>
        <div class="w-full mt-2 box p-16" v-else>
            <h1 class="text-center text-blue-800 text-3xl font-bold text-theme-1 mb-3">PERSONAL ACCIDENT INSURANCE</h1>
            <h1 class="text-center text-xl uppercase">POLICY SCHEDULE</h1>
            <div class="flex justify-end">
                <p class="text-blue-800">Policy No.: {{ detail.policy?.document_no }}</p>
            </div>
            <div class="flex justify-end">
                <p class="text-blue-800">Code: {{ detail.business_code }}</p>
            </div>
            <div class="w-full grid gap-4">
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">the insured name</div>
                    <div class="flex-1 font-semibold">
                        : {{ detail?.insured_name }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">correspondence address</div>
                    <div class="flex-1">
                        : {{ detail?.customer.address }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">business / occupation</div>
                    <div class="flex-1">
                        : {{ detail.customer.classification?.occupation }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">period of insurance</div>
                    <div class="flex-1">
                        : {{ detail?.insurance_period }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">coverage</div>
                    <div class="flex flex-1">
                        <span class="mr-1">: </span>
                        <div class="flex-1" v-html="detail?.product?.coverage"></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">automatic extensions</div>
                    <div class="flex flex-1">
                        <span>:</span>
                        <div class="flex-1 pl-1" v-if="detail?.clauses['AUTOMATIC_EXTENSION']">
                            <p class="" v-for="(clause, index) in detail.clauses['AUTOMATIC_EXTENSION']">
                                {{ clause?.name }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">optional extensions</div>
                    <div class="flex flex-1">
                        <span>:</span>
                        <div class="flex-1">
                            <div v-for="(exts, index) in detail.optional_extensions" class="pl-1">
                                <h6 class="font-semibold">{{ exts.extension_name }}</h6>
                                <div v-html="exts.extension_description"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">policy wording </div>
                    <div class="flex-1">
                        : Subject to {{ detail?.product?.name }} Policy Wording ({{ detail?.policy_wording_version }})
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">geographical limit </div>
                    <div class="flex-1">
                        : {{ detail?.coverage?.name }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">known accumulation limit or per conveyance limit </div>
                    <div class="flex-1">
                        : USD {{ formatCurrency(detail?.accumulation_limit_amount) }}
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">insured person </div>
                    <div class="flex flex-1">
                        <span class="mr-1">:</span>
                        <div class="flex-1" v-html="detail?.insured_person_note"></div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">Name List </div>
                    <div class="flex-1" @click="exportInsuredPersons">
                        : <span class="border-b border-blue-500 text-blue-700 hover:text-blue-500 cursor-pointer">{{
                            nameList }}</span>
                    </div>
                </div>
                <div class="w-full mt-4 ">
                    <table class="benefits-table border w-full">
                        <thead>
                            <tr class="capitalize bg-slate-100">
                                <th>risk no.</th>
                                <th>insured persons</th>
                                <th>accidental death (USD)</th>
                                <th>permanent disablement (USD)</th>
                                <th>accident medical expenses (USD)</th>
                                <th>premium per person (USD)</th>
                                <th>total premium (USD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(insuredP, index) in detail.insured_persons">
                                <td>{{ index + 1 }}</td>
                                <td>{{ insuredP.insured_person }}</td>
                                <td class="text-right">{{ insuredP.accidental_death }}</td>
                                <td class="text-right">{{ insuredP.permanent_disablement }}</td>
                                <td class="text-right">{{ insuredP.medical_expense }}</td>
                                <td class="text-right">{{ insuredP.premium_per_person }}</td>
                                <td class="text-right font-semibold">{{ insuredP.premium }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="capitalize text-right font-semibold" colspan="6">grand total</td>
                                <td class="text-right font-semibold">{{ Number(detail.total_premium).toFixed(2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">endorsements/clauses </div>
                    <div class="flex flex-1">
                        <span>:</span>
                        <div class="flex-1 pl-1" v-if="detail?.clauses['ENDORSEMENT']">
                            <p class="" v-for="(clause, index) in detail.clauses['ENDORSEMENT']"> {{ clause?.name }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">general exclusions </div>
                    <div class="flex flex-1">
                        <span class="mr-1">:</span>
                        <div class="flex-1 pl-1" v-if="detail?.clauses['EXCLUSION']">
                            <p class="" v-for="(clause, index) in detail.clauses['EXCLUSION']"> {{ clause?.name }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">warranty </div>
                    <div class="flex-1">
                        <div class="flex flex-1">
                            <span class="mr-1">: </span>
                            <div class="flex-1" v-html="detail?.warranty"></div>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">memorandum </div>
                    <div class="flex-1">
                        <div class="flex flex-1">
                            <span class="mr-1">: </span>
                            <div class="flex-1" v-html="detail?.memorandum"></div>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">subjectivity </div>
                    <div class="flex-1">
                        <div class="flex flex-1">
                            <span class="mr-1">: </span>
                            <div class="flex-1" v-html="detail?.subjectivity"></div>
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">remark </div>
                    <div class="flex flex-1">
                        <span class="mr-1">: </span>
                        <div class="flex-1" v-html="detail?.remark">
                        </div>
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">jurisdiction </div>
                    <div class="flex-1">
                        : Kingdom of Cambodia
                    </div>
                </div>
                <div class="flex">
                    <div class="w-72 uppercase font-semibold">issued on </div>
                    <div class="flex-1">
                        : {{ detail.issued_on }}
                    </div>
                </div>
                <div class="mt-16">
                    <div class="flex">
                        <div class="w-auto">
                            <div class="text-md font-bold mb-3 uppercase">
                                Phillip General Insurance (Cambodia) Plc.
                            </div>
                            <div class="mt-10">
                                <div class="relative" style="min-height: 100px;">
                                    <img v-if="detail.signature" :src="'/' + detail.signature" alt="Signature"
                                        style="max-height: 100px">
                                </div>
                                <hr class="my-3" />
                                <div class="text-md mb-3 font-medium">Authorised Signature</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Approval :options="approvalOpts" ref="approvalRef" :loading="state.approving" @confirm="handleConfirm" />
    <Endorsement ref="endorsementRef" @submit="handleSubmit" :saving="state.endorsing" />
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LoadingIndicator from "@/components/LoadingIndicator.vue"
import { formatCurrency } from "@/helpers"
import { hasPermission } from "@/services/auth.service";
import { useConfirm } from "primevue/useconfirm";
import policyService from '@/services/pa/policy.service';
import Approval from './Components/Approval.vue';
import Endorsement from './Components/Endorsement.vue';

const approvalOpts = ref([
    { label: "Approve", value: 'APV' },
    { label: "Reject", value: 'REJ' },
])
const approvalRef = ref(null)
const endorsementRef = ref(null)
const confirm = useConfirm();
const route = useRoute()
const router = useRouter()
const state = reactive({
    loading: true,
    deleting: false,
    approving: false,
    endorsing: false
})
const canEndorse = computed(() => {
    return detail.value?.policy?.status === 'APV' && detail.value?.policy?.approved_status === 'SBM' && !detail.value?.endorsement && hasPermission('PA_ENDORSEMENT', 'NEW');
})
const errors = ref({})
const detail = ref({
    policy: {},
    product: {},
    customer: {
        classification: {}
    },
    extensions: [],
    clauses: []
})
const nameList = computed(() => {
    return detail.value?.total_insured_persons + ' persons as per list attached'
})
const canEdit = computed(() => {
    return detail.value?.policy?.status === 'PND' && hasPermission('PA_POLICY', 'UPDATE');
})
const canDelete = computed(() => {
    return detail.value?.policy?.status === 'PND' && hasPermission('PA_POLICY', 'DELETE');
})
const canApprove = computed(() => {
    return detail.value?.policy?.approved_status === 'SBM' && detail.value?.policy?.status === 'PND' && hasPermission('PA_POLICY', 'APPROVE');
})
const printInvoiceUrlWithSignature = computed(() => {
    return `/pa/policy/${route.params?.id}/download-invoice/with-signature`;
});

const printInvoiceUrlWithoutSignature = computed(() => {
    return `/pa/policy/${route.params?.id}/download-invoice`;
});

const printCertificateUrl = computed(() => {
    return `/pa/policy/${route.params?.id}/download-certificate`;
});
// Add these computed properties
const printWithLetterHeadLink = computed(() => {
    return policyService.print(route.params?.id, 'en', { letterhead: 1 });
})
const printWithoutLetterHeadLink = computed(() => {
    return policyService.print(route.params?.id, 'en', { letterhead: 0 });
})
const printWithLetterHeadLinkKh = computed(() => {
    return policyService.print(route.params?.id, 'km', { letterhead: 1 });
})
const printWithoutLetterHeadLinkKh = computed(() => {
    return policyService.print(route.params?.id, 'km', { letterhead: 0 });
});

const printWithoutLetterHeadAndStampLink = computed(() => {
    return policyService.print(route.params?.id, 'en', { letterhead: 0, stamp: 0 });
});

const printWithoutLetterHeadAndStampLinkKh = computed(() => {
    return policyService.print(route.params?.id, 'km', { letterhead: 0, stamp: 0 });
});

const canPrintInvoice = computed(() => {
    return detail.value?.policy?.status === 'APV';
});

const refreshing = ref(false)
const refresh = async () => {
    refreshing.value = true
    await getDetail().then(() => refreshing.value = false);
}
const exportInsuredPersons = () => {
    location.href = '/pa/policy/' + route.params.id + '/export/insured-persons'
}
const handleEdit = () => {
    router.push({
        name: 'PAPolicyEdit',
        params: {
            id: route.params.id
        }
    })
}
const handleDelete = () => {
    confirm.require({
        message: 'Are you sure to delete this policy?',
        header: 'Confirmation',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined',
        acceptClass: 'p-button-danger p-button-outlined',
        rejectLabel: 'Cancel',
        acceptLabel: 'Delete',
        accept: () => {
            state.deleting = true
            policyService.delete(detail.value?.id).then(res => {
                notify(res.data.message, 'success')
                router.push({
                    name: 'PAPolicyIndex'
                })
            }).finally(() => state.deleting = false)
        },
        reject: () => {

        }
    });
}
const handleApprove = () => {
    approvalRef.value?.toggleDialog()
}
const handleConfirm = (data) => {
    state.approving = true
    policyService.approve(data, route.params.id).then(res => {
        notify(res.data.message, 'success')
        approvalRef.value.toggleDialog()
        getDetail();
    }).catch(err => {
        if (err.status === 422) errors.value = err.response.data.errors
        else notify(err.response.data.message, 'error')
    }).finally(() => state.approving = false)
}
const getDetail = () => {
    return policyService.detail(route.params.id).then((res) => {
        detail.value = res.data
    }).catch(err => {
        notify(err.response?.data?.message, 'error')
    })
        .finally(() => state.loading = false)
}
const handleEndorse = () => {
    endorsementRef.value?.toggleDialog()
}
const handleSubmit = (formData) => {
    state.endorsing = true
    policyService.endorse(formData, detail.value?.id).then(res => {
        notify(res.data.message,'success')
        endorsementRef.value?.toggleDialog()
        router.push({
            name:"PAEndorsementIndex"
        })
    })
    .catch(err => {
        if(err.status == 422) errors.value = err.response?.data?.errors
        else notify(err.response?.data?.message,'error')
    })
    .finally(() => {
        state.endorsing = false
    })
}
onMounted(async () => {
    await getDetail();
})
</script>

<style lang="scss" scoped>
.benefits-table {
    border-collapse: collapse;
    width: 100%;
}

.benefits-table th,
.benefits-table td {
    border: 1px solid #ddd;
    padding: 8px;
}

.benefits-table th {
    background-color: #f2f2f2;
    text-align: left;
}
</style>

<template>
  <div>
    <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
      <h3 class="text-lg">Travel Quotation Detail</h3>
      <div class="flex-1 flex justify-end">
        <button
          v-if="canApprove"
          class="bg-blue-800 text-white px-3 py-2.5 rounded-md hover:bg-blue-700 focus:outline-none"
          @click="approve"
          title="Approve"
        >
          Approve
        </button>
        <button
          v-if="canAccept"
          @click="accept"
          title="Accept"
          class="bg-blue-700 text-white px-3 py-2.5 rounded-md ml-1 hover:bg-blue-600 focus:outline-none"
        >
          Accept
        </button>
        <button
          v-if="canProceed"
          @click="proceedToPolicy"
          title="Proceed to policy"
          class="bg-blue-600 text-white px-3 py-2.5 rounded-md ml-1 hover:bg-blue-500 focus:outline-none"
        >
          Proceed to policy
        </button>
        <!-- <button
          class="bg-lime-600 text-white px-3 py-2.5 rounded-md ml-1 focus:outline-none hover:bg-lime-500"
          title="Export"
          @click="exportInsuredPersons"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M14 2H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V8l-6-6z" />
            <path d="M14 3v5h5M9.9 17.1L14 13M9.9 12.9L14 17" />
          </svg>
        </button>  -->
        <button
          class="bg-red-500 text-white py-2 px-3 rounded-md ml-1 focus:outline-none hover:bg-red-400"
          title="Delete"
          v-if="canDelete"
          @click="confirmDelete"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <polyline points="3 6 5 6 21 6"></polyline>
            <path
              d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
            ></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
        </button>
        <!-- <button class="bg-blue-700 px-3 py-2 rounded ml-1" @click="goEdit">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="#ffffff"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path
              d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"
            ></path>
            <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
          </svg>
        </button> -->
        <div class="dropdown ml-1">
                    <button class="dropdown-toggle btn btn-warning shadow-md mr-2" title="Print Quote"
                        id="print-button">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                            </path>
                        </svg>
                    </button>
                    <div class="dropdown-menu w-60">
                        <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLink" target="_blank">Letterhead (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLink" target="_blank">No Letterhead (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLink" target="_blank">No letterhead & Signature
                                (EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printStampWithoutLetterHeadLink" target="_blank">Signature with no
                                letterhead(EN)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithLetterHeadLinkKh" target="_blank">Letterhead (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadLinkKh" target="_blank">No Letterhead (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printWithoutLetterHeadAndStampLinkKh" target="_blank">No letterhead & Signature
                                (KH)</a>
                            <a class="flex items-center p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                                :href="printStampWithoutLetterHeadLinkKh" target="_blank">Signature with no
                                letterhead(KH)</a>
                        </div>
                    </div>
                </div>
        <button
          class="px-3 py-2 bg-blue-500 text-white rounded-md focus:outline-none hover:bg-blue-400 ml-1"
          title="Refresh"
          @click="refresh"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            :class="{ 'animate-spin': refreshing }"
            stroke-linejoin="round"
          >
            <path
              d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"
            />
          </svg>
        </button>
      </div>
    </div>
    <div v-if="loading" class="w-full box p-10 mt-2">
      <LoadingIndicator  />
    </div>
    <div class="w-full mt-2 box p-16" v-else>
      <h1 class="text-center text-blue-800 text-3xl font-bold text-theme-1 mb-3">
        {{ detail?.product?.name }}
      </h1>
      <h1 class="text-center text-xl uppercase">Insurance Quotation</h1>
      <div class="flex justify-end">
        <p class="text-blue-800">Quotation No: {{ detail.quotation?.document_no }}</p>
      </div>
      <div class="flex justify-end">
        <p class="text-blue-800">Code: {{ detail.business_code }}</p>
      </div>
      <div class="w-full grid gap-4">
        <div class="flex">
          <div class="w-72 uppercase font-semibold">the insured name</div>
          <div class="flex-1">
            : <span class="font-semibold">{{ detail?.insured_name }}</span>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">correspondence address</div>
          <div class="flex-1">: {{ detail?.customer.address }}</div>
        </div>
        <!-- <div class="flex">
          <div class="w-72 uppercase font-semibold">business / occupation</div>
          <div class="flex-1">: {{ detail.customer.classification?.occupation }}</div>
        </div> -->
        <div class="flex">
          <div class="w-72 uppercase font-semibold">period of insurance</div>
          <div class="flex-1">: {{ detail?.insurance_period }}</div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">Itinerary</div>
          <div class="flex flex-1">
            <span class="mr-1">: </span>
            <div class="flex-1" v-html="detail?.insurance_data?.country"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">policy wording</div>
          <div class="flex-1">
            : Subject to {{ detail?.product?.name }} Policy Wording ({{
              detail?.policy_wording_version
            }})
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">
            known accumulation limit or per conveyance limit
          </div>
          <div class="flex-1">: USD {{ formatCurrency(detail?.accumulation_limit) }}</div>
        </div>
        <div class="flex">
            <div class="w-72 uppercase font-semibold">insured person </div>
            <div class="flex flex-1">
                <span class="mr-1">:</span>
                <div class="flex-1" v-html="detail?.insured_person_note"></div>
            </div>
        </div>
        <div v-if="detail?.total_insured_persons == 1" class="w-full mt-4 ">
          <table class="benefits-table border w-full">
            <thead>
              <tr class="capitalize bg-slate-100">
                <th>No.</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>Passport No.</th>
                <th>Plan</th>
                <th class="text-right">Premium/Person</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(insuredP, index) in detail.data_details_view">
                <td>{{ index + 1 }}</td>
                <td>{{ insuredP.full_name }}</td>
                <td class="text-left">{{ insuredP.date_of_birth }}</td>
                <td class="text-left">{{ insuredP.passport_no }}</td>
                <td class="text-left">{{ insuredP.plan }}</td>
                <td class="text-right font-semibold">{{ insuredP.premium }}</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td class="capitalize text-right font-semibold" colspan="5">
                  grand total
                </td>
                <td class="text-right font-semibold">
                  {{ Number(detail.total_premium).toFixed(2) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div v-else class="flex">
            <div class="w-72 uppercase font-semibold">Name List</div>
            <div class="flex-1" @click="exportInsuredPersons">
                : <span class="border-b border-blue-500 text-blue-700 hover:text-blue-500 cursor-pointer">{{ nameList }}</span>
            </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">SCHEDULE OF BENEFITS</div>
          <div class="flex flex-1">
            <span class="mr-1"></span>
          </div>
        </div>
        <div class="w-full mt-4">
          <table class="benefits-table border w-full">
            <thead>
              <tr class="capitalize bg-slate-100">
                <th>Items</th>
                <th>Benefits and Limits</th>
                <th v-if="detail.coverageStandard.every(item => item.standard_limit !== null)" class="text-right">Standard Plan</th>
                <th v-if="detail.coverageStandard.every(item => item.deluxe_limit !== null)" class="text-right">Deluxe Plan</th>
                <th v-if="detail.coverageStandard.every(item => item.executive_limit !== null)" class="text-right">Executive Plan</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="coverage in detail.coverageStandard">
                <template v-if="!coverage.seq_no">
                  <td colspan="2" class="font-bold">{{ coverage.name }}</td>
                </template>
                <template v-else>
                  <td>{{ coverage.seq_no }}</td>
                  <td>{{ coverage.name }}</td>
                </template>
                <td v-if="detail.coverageStandard.every(item => item.standard_limit !== null)" class="text-right">{{ coverage.standard_limit }}</td>
                <td v-if="detail.coverageStandard.every(item => item.deluxe_limit !== null)" class="text-right">{{ coverage.deluxe_limit }}</td>
                <td v-if="detail.coverageStandard.every(item => item.executive_limit !== null)" class="text-right">{{ coverage.executive_limit }}</td>
              </tr>
              <template v-if="detail.coverageAdditional.length">
                <tr>
                  <td colspan="5" class="font-bold">Additional Benefits</td>
                </tr>
                <tr v-for="coverage in detail.coverageAdditional">
                  <td>{{ coverage.seq_no }}</td>
                  <td>{{ coverage.name }}</td>
                  <td class="text-right">{{ coverage.standard_limit }}</td>
                  <td class="text-right">{{ coverage.deluxe_limit }}</td>
                  <td class="text-right">{{ coverage.executive_limit }}</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">DEDUCTIBLE</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div v-for="item in detail.deductibles">
                <div class="mb-2">{{ item.label }}: {{ item.value }}</div>
              </div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">TERMS AND EXCLUSIONS</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1">
                <span>{{ clauses[0]?.label || "" }}</span>
                <br /><span
                  >Subject to Travel Insurance Policy Wording (TRV-V2022-12)</span
                >
              </div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">FOR CLAIM ENQUIRY & BENEFITS</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1">
                Claim Hotline: +855 17 682 682, +855 93 723 888 and Email:
                claim@phillipinsurance.com.kh
              </div>
            </div>
          </div>
        </div>
        <div v-if="detail.memorandum" class="flex">
          <div class="w-72 uppercase font-semibold">warranty</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.warranty"></div>
            </div>
          </div>
        </div>
        <div v-if="detail?.memorandum" class="flex">
          <div class="w-72 uppercase font-semibold">memorandum</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.memorandum"></div>
            </div>
          </div>
        </div>
        <div v-if="detail?.subjectivity" class="flex">
          <div class="w-72 uppercase font-semibold">subjectivity</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.subjectivity"></div>
            </div>
          </div>
        </div>
        <div class="flex" v-if="detail?.remark">
          <div class="w-72 uppercase font-semibold">remark</div>
          <div class="flex flex-1">
            <span class="mr-1">: </span>
            <div class="flex-1" v-html="detail?.remark"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">jurisdiction</div>
          <div class="flex-1">: {{ detail?.jurisdiction }}</div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">issued on</div>
          <div class="flex-1">: {{ detail.issued_on }}</div>
        </div>
        <div class="mt-16">
          <div class="flex">
            <div class="w-auto">
              <div class="text-md font-bold mb-3 uppercase">
                Phillip General Insurance (Cambodia) Plc.
              </div>
              <div class="my-2" :class="{ relative: detail.signature, 'min-h-40': true }">
                <img
                  v-if="detail.signature"
                  class="absolute max-h-20 top-2/3 -left-1/6"
                  :src="'/' + detail.signature"
                  alt=""
                />
                <img
                  v-if="detail.signature"
                  class="object-cover max-h-40"
                  src="/images/stamp/phillip_insurance.png"
                  alt=""
                />
              </div>
              <hr class="my-3" />
              <div class="text-md mb-3 font-medium">Authorised Signature</div>
            </div>
          </div>
          <div class="flex">
            <div class="w-1/3"></div>
            <div class="w-2/3">
              <div
                class="text-md mb-3 font-bold pt-1"
                style="text-decoration-line: underline; text-decoration-style: double"
              >
                ACCEPTANCE BY CLIENT:
              </div>
              <div class="text-md font-medium mb-3">
                We examine and understand the above terms and premium payment. We hereby
                accept and agree to the terms to issue the Policy with an effective on
                ...................................................
              </div>
            </div>
          </div>
          <br />
          <div class="flex mt-12">
            <div class="w-1/3"></div>
            <div class="w-2/3">
              <div class="text-md font-medium pt-1 border-t border-gray-200">
                Authorised Signature (Company Stamp if Applicable)
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ConfirmDialog
      ref="apvDialog"
      :options="approvalOpts"
      :loading="approvalLoading"
      title="Confirm Approval"
      :errors="errors"
      @confirm="confirmApprove"
    />
    <ConfirmDialog
      ref="acpDialog"
      :options="acceptanceOpts"
      :loading="approvalLoading"
      title="Confirm Acceptance"
      :errors="errors"
      @confirm="confirmAccept"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import QuoteService from "@/services/travel/quote.service";
import { useRoute, useRouter } from "vue-router";
import LoadingIndicator from "@/components/LoadingIndicator.vue";
import { formatCurrency } from "@/helpers";
import clauseService from "../../../services/product_config/clause.service";
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from './Components/ConfirmDialog.vue';
import { hasPermission } from "@/services/auth.service";


const route = useRoute();
const router = useRouter();
const loading = ref(true);
const confirm = useConfirm();
const apvDialog = ref(null);
const acpDialog = ref(null);
const approvalLoading = ref(false)
const detail = ref({
  policy: {},
  product: {},
  customer: {
    classification: {},
  },
  extensions: [],
  clauses: [],
  coverageStandard: [],
  coverageAdditional: [],
});
const errors = ref([])
const approvalOpts = ref([
    {
        label: 'Approve', value: 'APV'
    },
    {
        label: 'Reject', value: "REJ"
    }
])
const acceptanceOpts = ref([
    {
        label: 'Accept', value: 'ACP'
    },
    {
        label: 'Reject', value: "REJ"
    }
])
const canApprove = computed(() => {
    return !loading.value && detail.value?.quotation?.approved_status === 'PND' && hasPermission('TV_QUOTATION', 'APPROVE')
})
const canAccept = computed(() => {
    return !loading.value && detail.value?.quotation?.approved_status === 'APV' && detail.value?.quotation?.accepted_status === 'PND' && hasPermission('TV_QUOTATION', 'ACCEPT')
})
const canProceed = computed(() => {
    return !loading.value && detail.value?.quotation?.approved_status === 'APV' && detail.value?.quotation?.accepted_status === 'ACP' && !detail.value?.quotation?.policies?.length > 0 && hasPermission('TV_QUOTATION', 'PROCESS')
})
const canDelete = computed(() => {
    return !loading.value && detail.value?.quotation?.approved_status === 'PND' && hasPermission('TV_QUOTATION', 'DELETE')
})


const approve = () => {
  apvDialog.value?.toggleDialog();
};
const accept = () => {
  acpDialog.value?.toggleDialog();
};

const nameList = computed(() => {
    return detail.value?.total_insured_persons + ' persons as per list attached'
})

const clauses = ref([]);

const refreshing = ref(false);
const refresh = async () => {
  refreshing.value = true;
  await getDetail().then(() => (refreshing.value = false));
};

const getDetail = () => {
  loading.value = true;
  return QuoteService.detail(route.params.id)
    .then((res) => {
      detail.value = res.data;
    })
    .catch((err) => {
      notify(err.response?.data?.message, "error");
    })
    .finally(() => (loading.value = false));
};
const getClause = () => {
  loading.value = true;
  return clauseService
    .clauseType("TRAVEL")
    .then((res) => {
      clauses.value = res.data;
    })
    .catch((err) => {
      notify(err.response?.data?.message, "error");
    })
    .finally(() => (loading.value = false));
};

const proceedToPolicy = () => {
  confirm.require({
    message: "Do you want to proceed this quotation to policy?",
    header: "Proceed to policy",
    icon: "pi pi-shield",
    rejectClass: "p-button-secondary p-button-outlined",
    rejectLabel: "Cancel",
    acceptLabel: "Proceed",
    acceptClass: "p-button-info",
    accept: () => {
      QuoteService.proceed(route.params.id)
        .then((res) => {
          notify(res.data.message, "success");
          refresh();
        })
        .catch((err) => {
          notify(err.response.data.message, "error");
        });
    },
    reject: () => {},
  });
};

const confirmApprove = (form) => {
  approvalLoading.value = true;
  QuoteService.approve(route.params.id, form)
    .then((res) => {
      notify(res.data?.message, "success");
      apvDialog.value?.toggleDialog();
      refresh();
    })
    .catch((err) => {
      if (err.status == 422) errors.value = err.response?.data?.errors;
      else notify(err.response?.data.message, "error");
    })
    .finally(() => {
      approvalLoading.value = false;
    });
};
const confirmAccept = (form) => {
  approvalLoading.value = true;
  QuoteService.accept(route.params.id, form)
    .then((res) => {
      notify(res.data?.message, "success");
      acpDialog.value?.toggleDialog();
      refresh();
    })
    .catch((err) => {
      if (err.status == 422) errors.value = err.response?.data?.errors;
      else notify(err.response?.data.message, "error");
    })
    .finally(() => {
      approvalLoading.value = false;
    });
};
const confirmDelete = () => {
  confirm.require({
    message: "Do you want to delete this record?",
    header: "Delete",
    icon: "pi pi-exclamation-triangle text-red-500",
    rejectClass: "p-button-secondary p-button-outlined",
    rejectLabel: "No",
    acceptLabel: "Yes",
    acceptClass: "p-button-danger",
    rejectClass: "p-button-secondary px-2",
    accept: () => {
      QuoteService.delete(route.params.id)
        .then((res) => {
          notify(res.data?.message, "success");
          refresh();
        })
        .catch((err) => {
          notify(err.response?.data?.message, "error");
        })
        .finally(() => {});
    },
  });
};

const goEdit = () => {
    router.push({ name: "TravelQuotationEdit", params: { id: route.params.id } })
}
const exportInsuredPersons = () => {
    location.href = '/travel/quotations/' + route.params.id + '/export/insured-persons'
}
const printWithLetterHeadLink = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/en?letterhead=1`;
})
const printWithoutLetterHeadLink = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/en?letterhead=0`;
})
const printWithoutLetterHeadAndStampLink = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/en?letterhead=0&&stamp=0`;
})
const printStampWithoutLetterHeadLink = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/en?letterhead=0&&stamp=1`;
})
const printWithLetterHeadLinkKh = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/km?letterhead=1`;
})
const printWithoutLetterHeadLinkKh = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/km?letterhead=0`;
})
const printWithoutLetterHeadAndStampLinkKh = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/km?letterhead=0&&stamp=0`;
})
const printStampWithoutLetterHeadLinkKh = computed(() => {
    return `/travel/quotations/${route.params?.id}/download/km?letterhead=0&&stamp=1`;
})
onMounted(async () => {
  await getDetail();
  await getClause();
});
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
}
.benefits-table th.text-right {
  text-align: right;
}
</style>

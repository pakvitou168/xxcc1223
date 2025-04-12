<template>
  <div>
    <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
      <h3 class="text-lg">Travel Policy Detail</h3>
      <div class="flex-1 flex justify-end">
        <button
          v-if="canGenerateEndorsement"
          class="btn btn-primary shadow-md mr-2"
          title="Generate Endorsement"
          @click="openEndorsementDialog"
        >
          <span class="h-6 leading-6">Generate Endorsement</span>
        </button>
        <button
          v-if="canApprove"
          class="px-3 py-2 bg-blue-500 text-white rounded-md focus:outline-none hover:bg-blue-400 mr-2"
          title="Approve Policy"
          @click="openApproveDialog">
          <span>Policy Approval</span>
        </button>
        <Print v-if="detail?.policy?.id" :policyId="detail?.policy?.id" :status="detail?.policy.status" />
      </div>
    </div>
    <div v-if="loading" class="w-full box p-10 mt-2">
      <LoadingIndecator/>
    </div>
    <div class="w-full mt-2 box p-16" v-else>
      <h1 class="text-center text-blue-800 text-3xl font-semibold text-theme-1 mb-3">
        {{ detail.product ? detail.product.name : null }}</h1>
      <h1 class="text-center text-xl uppercase">POLICY SCHEDULE</h1>
      <div class="flex justify-end">
        <p class="text-blue-800">Policy No.: {{ detail.policy?.policy_no }}</p>
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
          <div class="flex flex-1">
            <span class="mr-1">: </span>
            <div class="flex-1" v-html="detail?.insurance_period"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">itinerary</div>
          <div class="flex flex-1">
            <span class="mr-1">: </span>
            <div class="flex-1 font-semibold" v-html="detail?.insurance_data?.country"></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">policy wording</div>
          <div class="flex-1">
            : Subject to {{ detail?.product?.name }} Policy Wording ({{ detail?.policy_wording_version }})
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">known accumulation limit or per conveyance limit</div>
          <div class="flex-1">
            : USD {{ formatCurrency(detail?.accumulation_limit) }}
          </div>
        </div>
        <Risk v-if="detail?.total_insured_persons == 1"
          :items="detail?.data_details_view"
          :totalPremium="detail.total_premium"
        />
        <div v-else class="flex">
          <div class="w-72 uppercase font-semibold">Name List</div>
          <div class="flex-1" @click="exportInsuredPersons">
            : <span class="border-b border-blue-500 text-blue-700 hover:text-blue-500 cursor-pointer">{{ nameList }}</span>
          </div>
        </div>
        <Benefit
          :coverages="detail?.coverage_standard"
          :additional_coverages="detail?.coverage_additional"
          :show_standard_limit="detail?.show_standard_limit"
          :show_deluxe_limit="detail?.show_deluxe_limit"
          :show_executive_limit="detail?.show_executive_limit"
        />
        <div class="flex" v-if="detail?.total_insured_persons > 1">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">TOTAL PREMIUM:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm font-bold mb-3"> {{ detail.total_premium   }}</div>
          </div>
        </div>
        <div class="flex" v-if="detail?.total_insured_persons">
          <div class="w-72">
            <div class="text-sm font-bold mb-3">DEDUCTIBLE:</div>
          </div>
          <div class="flex-1" v-if="detail.total_insured_persons == 1">
            <div class="text-sm font-bold mb-2">It is applicable to each and every claim for:</div>
            <div class="text-sm mb-2" v-for="item in detail.deductibles" :key="item.comp_code">
              USD {{ item.value +' '+ item.label }}
            </div>
          </div>
          <div class="flex-1" v-else-if="detail.total_insured_persons > 1">
            <div class="text-sm font-bold mb-2">As per list attached</div>
          </div>
        </div>

        <div class="flex">
          <div class="w-72 uppercase font-semibold">TERMS AND EXCLUSIONS</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1">
                <span>{{ clauses[0]?.label || '' }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">FOR CLAIM ENQUIRY & BENEFITS</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1 font-semibold">Claim Hotline: {{ lovs.claim_enquiry_benefit[0]?.label }},
                {{ lovs.claim_enquiry_benefit[1]?.label }} and Email: {{ lovs.claim_enquiry_benefit[2]?.label }}
              </div>
            </div>
          </div>
        </div>

        <div class="flex">
          <div class="w-72 uppercase font-semibold">warranty</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.warranty"></div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">memorandum</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.memorandum"></div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">subjectivity</div>
          <div class="flex-1">
            <div class="flex flex-1">
              <span class="mr-1">: </span>
              <div class="flex-1" v-html="detail?.subjectivity"></div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">remark</div>
          <div class="flex flex-1">
            <span class="mr-1">: </span>
            <div class="flex-1" v-html="detail?.remark">
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">jurisdiction</div>
          <div class="flex-1">
            : Kingdom of Cambodia
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">ISSUED ON</div>
          <div class="flex-1">
            : {{ issuedDate() }}
          </div>
        </div>
        <div class="flex">
          <div class="w-72 uppercase font-semibold">ISSUED BY</div>
          <div class="flex-1">
            : {{ detail.issued_by }}
          </div>
        </div>

        <div class="flex">
          <div class="w-auto">
            <div class="text-md font-bold mb-3 uppercase">
              Phillip General Insurance (Cambodia) Plc.
            </div>
            <div
              class="my-2"
              :class="{ relative: hasSignature }"
              style="min-height: 150px"
            >
              <img alt="img-over"
                 v-if="hasSignature && canShowSignature"
                 class="img-over"
                 :src="signatureUrl"
                 style="max-height: 150px"
              />
              <img alt="img-under"
                 v-if="hasSignature && canShowSignature"
                 class="img-under"
                 src="/images/stamp/phillip_insurance.png"
                 style="max-height: 150px"
              />
            </div>

            <hr class="my-3"/>

            <div class="text-md mb-3 font-medium">Authorised Signature</div>
          </div>
        </div>
      </div>
    </div>

    <ApproveDialog
      :isVisible="approveDialog"
      header="Policy Approval"
      :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]"
      value="APV"
      @hideDialog="hideDialog"
      @confirm="approve"
      :saving="savingApproval"
    />
    <EndorsementDialog
      header="Generate Endorsement"
      :visible="endorsementDialog.opened"
      :submitting="endorsementDialog.submitting"
      :types="endorsementTypes"
      :validFromDate="validPeriod.from"
      :validToDate="validPeriod.to"
      :typeErrors="endorsementDialog.errors?.type ?? []"
      :effectiveDateErrors="endorsementDialog.errors?.effective_date ?? []"
      @hide="
        () => {
          endorsementDialog.opened = false;
          endorsementDialog.submitting = false;
          endorsementDialog.errors = {};
        }
      "
      @confirm="generateEndorsement"
    />
  </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue';
import PolicyService from '@/services/travel/policy/policy.service.js'
import {useRoute, useRouter} from 'vue-router';
import LoadingIndecator from "@/components/LoadingIndicator.vue"
import {formatCurrency} from "@/helpers"
import clauseService from '@/services/travel/policy/clause.service.js';
import moment from "moment";
import ApproveDialog from './ApproveDialog.vue';
import PolicyVerificationService from "@/services/travel/policy/policyVerification.service.js";
import deductibleDataService from "@/services/travel/policy/deductibleData.service.js";
import endorsementService from "@/services/travel/policy/endorsement.service";
import Print from './Components/Print.vue';
import Risk from './Components/Risk.vue';
import Benefit from './Components/Benefit.vue';
import EndorsementDialog from "./Components/EndorsementDialog.vue";
const route = useRoute();
const ERROR_MESSAGE = ref("Something went wrong!");
const SUCCESS_MESSAGE = ref("Success!");
const router = useRouter();
const loading = ref(true);
const endorsementTypes = ref({});
const validPeriod = ref({});
const endorsementDialog = ref({
  opened: false,
  submitting: false,
  errors: {},
});
const detail = ref({
  policy: {},
  product: {},
  customer: {
    classification: {}
  },
  extensions: [],
  clauses: [],
  coverageStandard: [],
  coverageAdditional: [],
  signature: null,
  updated_at: null,
  created_at: null,
});
const deductibleData = ref(null);
const approveDialog = ref(false);
const canGenerateEndorsement = ref(false);
const submitted = ref(false);
const savingApproval = ref(false);
const isReinsuranceCompleted = ref(false);
const isConfigCompleted = ref(false);

const nameList = computed(() => {
  return detail.value?.total_insured_persons + ' persons as per list attached'
})

const canApprove = computed(() => {
  return detail.value?.policy?.approved_status === 'SBM' &&
      detail.value?.policy?.status === 'PND'
})
const exportInsuredPersons = () => {
  location.href = '/travel/policies/' + route.params.id + '/export/insured-persons'
}
const canPrintInvoice = computed(() => {
  return detail.value?.policy?.status === 'APV'
      ;
})

const clauses = ref([]);
const lovs = ref({
  payee_types: [],
  claim_enquiry_benefit: [],
});

const hasSignature = computed(() => {
  return !!detail.value?.signature;
});

const signatureUrl = computed(() => {
  return detail.value?.signature ? '/' + detail.value.signature : '';
});

const canShowSignature = computed(() => {
  return detail.value.policy?.status === "APV";
});

const refreshing = ref(false);
const refresh = async () => {
  refreshing.value = true;
  await getDetail().then(() => refreshing.value = false);
};

const getDetail = () => {
  loading.value = true;
  return PolicyService.detail(route.params.id).then((res) => {
    detail.value = res.data;
  }).catch(err => {
    notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
  })
      .finally(() => loading.value = false);
};

const getClause = () => {
  return clauseService.clauseType('TRAVEL').then((res) => {
    clauses.value = res.data;
  }).catch(err => {
    // notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
    console.error(err);

  })
      .finally(() => loading.value = false);
};

const getLovs = () => {
  loading.value = true;
  return PolicyService.getLovs().then((res) => {
    lovs.value.claim_enquiry_benefit = res.data?.claim_enquiry_benefit;
  }).catch(err => {
    notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
  })
  .finally(() => loading.value = false);
};


const issuedDate = () => {
  let date = detail.value.updated_at ?? detail.value.created_at;
  return moment(date).format("DD/MM/YYYY");
};

const isPolicyReinsuranceCompleted = () => {
  loading.value = true
  return PolicyVerificationService.isPolicyReinsuranceCompleted(route.params.id)
      .then((res) => {
        isReinsuranceCompleted.value = res.data;
      }).catch(err => {
        notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
      })
      .finally(() => loading.value = false);
};

const isPolicyConfigurationCompleted = () => {
  loading.value = true
  return PolicyVerificationService.isPolicyConfigurationCompleted(route.params.id)
      .then((res) => {
        isConfigCompleted.value = res.data;
      }).catch(err => {
        notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
      })
      .finally(() => loading.value = false);
};

const openApproveDialog = () => {
  approveDialog.value = true;
  submitted.value = false;
  endorsementDialog.opened = true;
};

const hideDialog = () => {
  approveDialog.value = false;
  submitted.value = false;
};

const approve = (form) => {
  submitted.value = true;
  if (form.status && form.reason) {
    savingApproval.value = true;
    axios.post(`/travel/policies/approve/${detail.value.policy?.id}`, {
      approved_status: form.status,
      approved_reason: form.reason,
    }).then(response => {
      if (response.data.success) {
        notify(response.data.message, 'Success');
        router.push({name: 'TravelPolicyIndex'});
      }
    }).catch(err => {
      notify(err?.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
    }).finally(() => {
      savingApproval.value = false;
    });
  }
};

const fetchDeductibleData = () => {
  loading.value = true
  return deductibleDataService.get(route.params.id)
      .then((res) => {
        deductibleData.value = res.data.deductible_data;
      }).catch(err => {
        notify(err.response?.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
      })
      .finally(() => loading.value = false);
};
const checkCanGenerateEndorsement = () => {
  return endorsementService.canGenerate(detail.value?.policy.id).then((res) => {
    canGenerateEndorsement.value = res.data.can_generate;
  });
};
const generateEndorsement = (form) => {
  endorsementDialog.value.submitting = true;
  return endorsementService
    .generate(form, detail.value.policy?.id)
    .then((res) => {
      notify(res.data?.message || SUCCESS_MESSAGE.value, "success", "bottom-right");
      router.push({ name: "TravelEndorsementIndex" });
    })
    .catch((err) => {
      if (err.response?.status === 422) {
        endorsementDialog.value.errors = err.response.data.errors;
        notify(err.response?.data.message || ERROR_MESSAGE.value, "error" ,"bottom-right");
      } else {
        notify(err.response?.data.message || ERROR_MESSAGE.value, "error" ,"bottom-right");
      }
    })
    .finally(() => {
      endorsementDialog.value.submitting = false;
    });
};

const openEndorsementDialog = () => {
  if (Object.keys(endorsementTypes.value).length === 0) {
    listEndorsementTypes();
  }
  //
  // if (Object.keys(validPeriod.value).length === 0) {
  //   getValidPeriod();
  // }

  endorsementDialog.value.opened = true;
};

const listEndorsementTypes = () => {
  return endorsementService
    .listEndorsementTypes()
    .then((res) => (endorsementTypes.value = res.data));
};
const getValidPeriod = () => {
  return endorsementService
    .getValidPeriod(route.params.id)
    .then((res) => (validPeriod.value = res.data));
};

onMounted(async () => {
  await getDetail();
  await getClause();
  await getLovs();
  await isPolicyReinsuranceCompleted();
  await isPolicyConfigurationCompleted();
  await fetchDeductibleData();
  await checkCanGenerateEndorsement();
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
  text-align: left;
}

.contact-item {
  display: inline-block;
}
</style>

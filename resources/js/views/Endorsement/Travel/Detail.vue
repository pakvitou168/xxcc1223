<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">Travel Endorsement Detail</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canGenerateEndorsement" class="btn btn-primary shadow-md mr-2" title="Generate Endorsement"
                @click="openEndorsementDialog">
          <span class="h-6 leading-6">Generate Endorsement</span>
        </button>
        <div v-if="canExportInsuredPerson || canExportAllInsuredPerson" class="dropdown">
          <button class="dropdown-toggle btn btn-success shadow-md mr-2" title="Export Excel">
            <DocumentTextIcon/>
          </button>
          <div class="dropdown-menu w-56">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a v-if="canExportInsuredPerson"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 @click="exportInsuredPerson" target="_blank">Endorsements</a>
              <a v-if="canExportAllInsuredPerson"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 @click="exportAllInsuredPerson" target="_blank">
                Update List for All
              </a>
            </div>
          </div>
        </div>
        <button v-if="canApproveCond" class="btn btn-primary shadow-md mr-2" title="Approve Endorsement"
                @click="openApproveDialog">
          <span class="h-6 leading-6">Endorsement Approval</span>
        </button>
        <button v-if="canDeleteCond" class="btn btn-danger mr-2 intro-x" title="Delete" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdateCond" :to="{ name: 'TravelEndorsementEdit', params: { id: id } }">
          <button class="btn btn-primary intro-x" title="Edit">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
              </path>
            </svg>
          </button>
        </router-link>
        <div class="dropdown ml-2">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
              </path>
            </svg>
          </button>
          <div class="dropdown-menu w-56">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a v-if="canPrintInvoice && isApproved"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printInvoiceUrlWithSignature" target="_blank">Invoice (Signature)</a>
              <a v-if="canPrintInvoice"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printInvoiceUrlWithoutSignature" target="_blank">Invoice (No Signature)</a>
              <a v-if="canPrintCreditNote && isApproved"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printCreditNoteUrlWithSignature" target="_blank">Credit Note (Signature)</a>
              <a v-if="canPrintCreditNote"
                 class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                 :href="printCreditNoteUrlWithoutSignature" target="_blank">Credit Note (No Signature)</a>
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printLinkLetterHead" target="_blank">Endorsement (Letterhead)</a>
              <a
                class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printLinkNoLetterHead" target="_blank">Endorsement (No Letterhead)</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <!--      <Detail v-if="id" :id="id" :endorsementId="id" :endorsementStatus="formValues.status" :endorsement="formValues"-->
      <!--        :documentNo="documentNo" @get-total-premium="getTotalPremium" />-->
    </div>

    <ApproveDialog
      header="Endorsement Approval"
      :isVisible="approveDialog"
      :submitted="submitted"
      :options="[{ value: 'APV', label: 'Approve' }, { value: 'REJ', label: 'Reject' }]"
      value="APV"
      @hideDialog="hideDialog"
      @confirm="approve"/>

    <EndorsementDialog header="Generate Endorsement" :isVisible="endorsementDialog.opened"
                       :submitting="endorsementDialog.submitting" :types="endorsementTypes"
                       :validFromDate="validPeriod.from"
                       :validToDate="validPeriod.to" :typeErrors="endorsementDialog.errors?.type ?? []"
                       :effectiveDateErrors="endorsementDialog.errors?.effective_date ?? []" @hide="() => {
        endorsementDialog.opened = false;
        endorsementDialog.submitting = false;
        endorsementDialog.errors = {};
      }
        " @confirm="generateEndorsement"/>
  </div>
</template>

<script setup>
import {computed, onMounted, reactive, ref} from 'vue';
import {useRoute, useRouter} from 'vue-router';
import ApproveDialog from "../../Policy/Travel/ApproveDialog.vue";
import endorsementService from "@/services/travel/policy/endorsement.service";
// Import user permissions functionality (converted from mixins)
import {hasPermission} from "@/services/auth.service";
import EndorsementDialog from "./EndorsementDialog.vue";
import DocumentTextIcon from '@/components/Icons/DocumentTextIcon.vue';
import axios from 'axios';
import {useConfirm} from 'primevue/useconfirm';

// Setup hooks
const route = useRoute();
const router = useRouter();
const confirm = useConfirm();

// State
const ERROR_MESSAGE = ref("Something went wrong!");
const SUCCESS_MESSAGE = ref("Success!");
const id = ref(route.params.id ?? null);
const canGenerateEndorsement = ref(false);
const formValues = ref({});
const approveDialog = ref(false);
const submitted = ref(false);
const totalPremium = ref(null);
const functionCode = "ENDORSEMENT";
const isReinsuranceCompleted = ref(false);
const isConfigCompleted = ref(false);
const showEndorsementDialog = ref(false);
const insured_length = ref(0);
const endorsementDialog = reactive({
  opened: false,
  submitting: false,
  errors: {},
});
const endorsementTypes = ref({});
const validPeriod = ref({});
const canExportAllInsuredPerson = ref(false);

// Computed properties
const documentNo = computed(() => {
  return formValues.value?.document_no;
});

const canApproveCond = computed(() => {
  // If don't have permission to approve
  if (!hasPermission(functionCode, 'APPROVE')) return false;

  // If endorsement data is not yet completed
  if (!isReinsuranceCompleted.value || !isConfigCompleted.value) return false;

  // If endorsement is not yet approved and approved_status as submit_status is Submitted
  return (
    formValues.value.status === "PND" &&
    formValues.value.endorsement?.approved_status === "SBM"
  );
});

const canUpdateCond = computed(() => {
  if (
    formValues.value.status === "PND" &&
    formValues.value.endorsement?.approved_status === "PRG"
  )
    return true;
  else if (
    formValues.value.status === "PND" &&
    formValues.value.endorsement_type === "GENERAL"
  )
    return true;
  else return false;
});

const isApproved = computed(() => {
  return formValues.value.status === 'APV';
});

const canDeleteCond = computed(() => {
  return formValues.value.status === "PND";
});

const canPrintInvoice = computed(() => {
  return totalPremium.value > 0;
});

const canPrintCreditNote = computed(() => {
  return totalPremium.value < 0;
});

const printLinkLetterHead = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-endorsement?letterhead=1`;
});

const printLinkNoLetterHead = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-endorsement?letterhead=0`;
});

const printInvoiceUrlWithoutSignature = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-invoice?signature=0`;
});

const printInvoiceUrlWithSignature = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-invoice?signature=1`;
});

const printCreditNoteUrlWithoutSignature = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-credit-note?signature=0`;
});

const printCreditNoteUrlWithSignature = computed(() => {
  return `/travel/endorsement-services/${id.value}/download-credit-note?signature=1`;
});

const canExportInsuredPerson = computed(() => {
  return insured_length.value > 0 || formValues.value?.endorsement_type == 'GENERAL';
});

// Methods
const checkCanExportAllInsuredPerson = () => {
  axios.get(`/travel/endorsement-services/can-export-all-insured-person/${id.value}`).then(response => {
    canExportAllInsuredPerson.value = response.data;
  });
};

const openApproveDialog = () => {
  approveDialog.value = true;
  submitted.value = false;
};

const getTotalPremium = (total_premium) => {
  totalPremium.value = total_premium;
};

const hideDialog = () => {
  approveDialog.value = false;
  showEndorsementDialog.value = false;
  submitted.value = false;
};

const getEndorsement = () => {
  if (id.value) {
    endorsementService
      .detail(id.value)
      .then((response) => {
        formValues.value = response.data;
        insured_length.value = formValues.value.insured_persons_count;

        /*console.log('abc', {
          insured_persons_count:formValues.value.insured_persons_count,
          insured_length:insured_length.value,
          insured_length_c:insured_length.value > 0,
        })*/

      })
      .then(() => isPolicyConfigurationCompleted())
      .then(() => isPolicyReinsuranceCompleted());
  }
};

const isPolicyReinsuranceCompleted = () => {
  if (id.value)
    axios
      .get(`/travel/policy-services/is-policy-reinsurance-completed/${id.value}`)
      .then((response) => {
        isReinsuranceCompleted.value = response.data;
      });
};

const isPolicyConfigurationCompleted = () => {
  if (id.value)
    axios
      .get(
        `/travel/policy-services/is-policy-configuration-completed/${id.value}`
      )
      .then((response) => {
        isConfigCompleted.value = response.data;
      });
};

const approve = (form) => {
  submitted.value = true;

  if (form.status && form.reason) {
    endorsementService
      .approve(
        {
          approved_status: form.status,
          approved_reason: form.reason,
        },
        id.value
      )
      .then((response) => {
        if (response.data.success) {
          notify(response.data.message || SUCCESS_MESSAGE.value, "success", "bottom-right");
          router.push({name: "TravelEndorsementIndex"});
        }
      })
      .catch((err) => {
        let error = err?.response;
        notify(error.data?.message || ERROR_MESSAGE.value, "error", "bottom-right");
        if (error.status === 409) {
          router.push({name: "TravelEndorsementIndex"});
        }
      }).finally(() => {
      submitted.value = false;
    });
  }
};

const generateInvoice = () => {
  axios.post('/travel/endorsement-services/generate-invoice', {
    documentNo: formValues.value.document_no,
    requestType: 'INVOICE'
  }).catch(err => {
    console.log(err);
  });
};

const generateCreditNote = () => {
  axios.post('/travel/endorsement-services/generate-credit-note', {
    documentNo: formValues.value.document_no,
    requestType: 'CREDIT_NOTE'
  }).catch(err => {
    console.log(err);
  });
};

const handleDelete = (id) => {
  confirm.require({
    message: 'Do you want to delete this record?',
    header: 'Delete',
    icon: 'pi pi-exclamation-triangle text-red-500',
    rejectClass: 'p-button-secondary p-button-outlined',
    rejectLabel: 'No',
    acceptLabel: 'Yes',
    acceptClass: 'p-button-danger',
    accept: () => {
      axios
        .delete(`/travel/endorsements/${id}`)
        .then((response) => {
          if (response.data.success) {
            // refresh table
            notify(response.data.message, "success");
            router.push({
              name: "TravelEndorsementIndex",
            });
          }
        })
        .catch((err) => {
          let error = err.response;
          notify(error?.data?.message || ERROR_MESSAGE.value , "error", "bottom-right");
          if (error.status === 409) {
            // Redirect to index page on conflict
            router.push({name: "TravelEndorsementIndex"});
          }
        });
    },
  });
};

const checkCanGenerateEndorsement = () => {
  endorsementService.canGenerate(id.value).then((res) => {
    canGenerateEndorsement.value = res.data;
  });
};

const generateEndorsement = (form) => {
  endorsementDialog.submitting = true;

  endorsementService
    .generate(form, id.value)
    .then((res) => {
      notify(res.data?.message, "success");

      router.push({name: "TravelEndorsementIndex"});
    })
    .catch((err) => {
      if (err.response?.status === 422) {
        endorsementDialog.errors = err.response.data.errors;
        notify(err.response.data?.message || ERROR_MESSAGE.VALUE, "error", "bottom-right");
      } else {
        notify(err.response.data?.message || ERROR_MESSAGE.VALUE, "error", "bottom-right");
      }
    })
    .finally(() => {
      endorsementDialog.submitting = false;
    });
};

const getValidPeriod = () => {
  endorsementService
    .getValidPeriod(id.value)
    .then((res) => (validPeriod.value = res.data));
};

const openEndorsementDialog = () => {
  if (Object.keys(endorsementTypes.value).length === 0) {
    listEndorsementTypes();
  }

  if (Object.keys(validPeriod.value).length === 0) {
    getValidPeriod();
  }

  endorsementDialog.opened = true;
};

const listEndorsementTypes = () => {
  endorsementService
    .listEndorsementTypes()
    .then((res) => (endorsementTypes.value = res.data));
};

const exportInsuredPerson = () => {
  location.href = '/travel/endorsement-services/' + formValues.value?.data_id + '/export-insured-person/' + documentNo.value;
};

const exportAllInsuredPerson = () => {
  location.href = `/travel/endorsement-services/${formValues.value?.data_id}/export-all-insured-person/${documentNo.value}`;
};

// Lifecycle hooks
onMounted(() => {
  getEndorsement();
  checkCanGenerateEndorsement();
  checkCanExportAllInsuredPerson();
});

// Expose methods for template
defineExpose({
  openApproveDialog,
  hideDialog,
  handleDelete,
  openEndorsementDialog,
  approve,
  generateEndorsement,
  exportInsuredPerson,
  exportAllInsuredPerson
});
</script>
<template>
  <div>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8 detail-title">
      <h2 class="text-lg font-medium mr-auto">Claim Registration</h2>
      <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
        <button v-if="canApprove" class="btn btn-primary shadow-md mr-2" @click="openDialog"
          :title="formValues.approved_status == null ? 'Approve registration' : 'Approve schema data'">
          <span class="h-6 leading-6">{{ formValues.approved_status == null ? 'Approve' : 'Approve Schema' }}</span>
        </button>
        <div class="dropdown">
          <button class="dropdown-toggle btn btn-primary shadow-md mr-2" title="Print">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
              </path>
            </svg>
          </button>
          <div class="dropdown-menu w-52">
            <div class="dropdown-menu__content box dark:bg-dark-1 p-2">
              <a class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithLetterHeadUrl" target="_blank">Registration Letterhead</a>
              <a class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printWithoutLetterHeadUrl" target="_blank">Registration No Letterhead</a>
              <a v-if="canprintSchema" class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printSchemaWithLetterHeadUrl" target="_blank">Schema Letterhead</a>
              <a v-if="canprintSchema" class="items-center block p-2 transition duration-300 ease-in-out bg-white hover:bg-gray-200 rounded-md cursor-pointer"
                :href="printSchemaWithoutLetterHeadUrl" target="_blank">Schema No Letterhead</a>
            </div>
          </div>
        </div>
        <button v-if="canDelete" class="btn btn-danger mx-1 intro-x" @click="handleDelete(id)">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
            </path>
          </svg>
        </button>
        <router-link v-if="canUpdate"
          :to="{ name: formValues.approved_status == 'APV' ? 'ClaimHSRegisterSchema' : 'ClaimHSRegisterEdit', params: { id: id } }">
          <button class="btn btn-primary mx-1 intro-x"
            :title="formValues.approved_status == null ? 'Update registration' : (formValues.is_schema_created ? 'Update schema data':'Create Schema' )">
            <svg v-if="formValues.approved_status == null" class="w-6 h-6" fill="none" stroke="currentColor"
              viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
              </path>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="16"></line>
              <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
          </button>
        </router-link>
      </div>
    </div>
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="py-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            Claim Registration
          </div>
        </div>
      </div>
      <div class="grid 2xl:grid-cols-3 px-5 sm:px-16">
        <div class="col-span-2">
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Claimant Information</div>

            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Name</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.insured_person.name }}
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Gender</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.insured_person.gender }}
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Date of birth</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.insured_person.date_of_birth }}
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Age</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.insured_person.age }}
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Occupation</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.insured_person.occupation }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="grid 2xl:grid-cols-3 px-5 sm:px-16 pb-10">
        <div class="col-span-2">
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Claim Details</div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Policy No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">
                  : {{ formValues.policy?.document_no }}
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Claims No.</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.claim_no }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Date of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.date_of_loss }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Date of Notification</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.notification_date }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Cause of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.detail?.cause}}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Location of Loss</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.location_of_loss }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">Loss Description</div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: {{ formValues.loss_description }}</div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold mb-1.5">
                  Insurance Cover Period
                </div>
              </div>
              <div class="w-2/3">
                <div class="text-base">: From {{ formValues.insured_period_from }} to {{ formValues.insured_period_to }}</div>
              </div>
            </div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Claim Estimation: <span>{{ formatCurrency(formValues.total_reserve_amount)
                }}</span></div>
          </div>
          <div class="pt-6">
            <div class="text-xl font-bold mb-2.5">Reinsurance: </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold">Reinsurance Type</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base font-bold text-center">Share Rate</div>
                  <div class="text-base font-bold text-right">Reserve Amount</div>
                </div>
              </div>
            </div>
            <div class="flex" v-for="(detail, index) in formValues.reinsurances" :key="index">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base">{{ detail.name }}</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base text-center">{{ detail.share_rate }}%</div>
                  <div class="text-base text-right">{{ formatCurrency(detail.reserve_amount) }}</div>
                </div>
              </div>
            </div>
            <div class="flex">
              <div class="w-1/3 text-right pr-5">
                <div class="text-base font-bold">TOTAL</div>
              </div>
              <div class="w-2/3 grid grid-cols-2">
                <div class="grid grid-cols-2">
                  <div class="text-base font-bold text-center">{{ totalShareReinsurance }}%</div>
                  <div class="text-base font-bold text-right">{{ formatCurrency(totalAmountReinsurance) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="w-full text-right px-4 py-4">
        <router-link :to="{ name: 'ClaimHSRegisterIndex' }" class="btn btn-outline-secondary w-24 mr-1">
          Back
        </router-link>
      </div>
    </div>

    <ApproveDialog :isVisible="showDialog"
      :header="formValues.approved_status == null ? 'Register Approval' : 'Schema Data Approval'" :submitted="submitted"
      :options="{ APV: 'Approve', REJ: 'Reject' }" value="APV" @hideDialog="hideDialog" @confirm="approve" />
  </div>
</template>

<script>
import ApproveDialog from "@/components/Dialogs/ApproveDialog.vue";
import RegisterService from "@/services/claim/travel/claim_register.service";
import { hasPermission } from "@/services/auth.service";

export default {
  components: {
    ApproveDialog,
  },

  data() {
    return {
      id: this.$route.params.id ?? null,
      showDialog: false,
      submitted: false,
      formValues: {
        insured_person: {},
        detail: {},
        data_master: {},
        policy: {},
      },
    };
  },

  computed: {
    canprintSchema() {
      return this.formValues.is_schema_created
    },
    printWithLetterHeadUrl() {
      return RegisterService.printUrl(this.id, "en", 1);
    },
    printWithoutLetterHeadUrl() {
      return RegisterService.printUrl(this.id, "en", 0);
    },
    printSchemaWithLetterHeadUrl() {
      return RegisterService.printSchemaUrl(this.id, "en", 1);
    },
    printSchemaWithoutLetterHeadUrl() {
      return RegisterService.printSchemaUrl(this.id, "en", 0);
    },
    getInsuranceCoverPeriod() {
      return `${this.formatDate(
        this.formValues.data_master?.effective_date_from
      )} To ${this.formatDate(this.formValues.data_master?.effective_date_to)}`;
    },
    canApprove() {
      let canApprovePermission = hasPermission("HS_CLAIM_REGISTER", "APV");
      if (!canApprovePermission) return false;
      return this.formValues?.approved_status == null || (this.formValues?.schema_approved_status == null && this.formValues.is_schema_created);
    },
    canUpdate() {
      let canUpdatePermission = hasPermission("HS_CLAIM_REGISTER", "UPD");
      if (!canUpdatePermission) return false;
      return this.formValues?.approved_status == null || this.formValues?.schema_approved_status == null;
    },
    canDelete() {
      let canDeletePermission = hasPermission("HS_CLAIM_REGISTER", "DEL");
      if (!canDeletePermission) return false;
      return this.formValues?.approved_status !== "APV";
    },
    totalShareReinsurance() {
      return this.formValues.reinsurances ? this.formValues.reinsurances.reduce((a, b) => a + (Number(b['share_rate']) || 0), 0) : 0
    },
    totalAmountReinsurance() {
      return this.formValues.reinsurances ? this.formValues.reinsurances.reduce((a, b) => a + (Number(b['reserve_amount']) || 0), 0) : 0
    }
  },

  methods: {
    openDialog() {
      this.showDialog = true;
      this.submitted = false;
    },
    hideDialog() {
      this.showDialog = false;
      this.submitted = false;
    },
    approve(form) {
      this.submitted = true;
      if (form.status && form.comment) {
        if (this.formValues.approved_status == null) {
          RegisterService.approve(form, this.id)
            .then((res) => {
              if (res.data?.success) {
                this.$notify(
                  {
                    group: "bottom",
                    title: "Success",
                    text: res.data?.message,
                  },
                  4000
                );
                this.$router.push({ name: "ClaimHSRegisterIndex" });
              }
            })
            .catch((err) => {
              this.$notify(
                {
                  group: "bottom",
                  title: "Error",
                  text: err?.response?.data?.message,
                },
                4000
              );
              if(err?.response?.status === 403){
                this.getDetail();
              }
            });
        } else {
          RegisterService.approveSchema(this.id, form)
            .then((res) => {
              if (res.data?.success) {
                this.$notify(
                  {
                    group: "bottom",
                    title: "Success",
                    text: res.data?.message,
                  },
                  4000
                );
                this.$router.push({ name: "ClaimHSRegisterIndex" });
              }
            })
            .catch((err) => {
              this.$notify(
                {
                  group: "bottom",
                  title: "Error",
                  text: err?.response?.data?.message,
                },
                4000
              );
              if(err?.response?.status === 403){
                this.getDetail();
              }
            });
        }

      }
    },

    handleDelete(id) {
      this.$confirm.require({
        message: "Do you want to delete this record?",
        header: "Delete",
        icon: "pi pi-info-circle",
        acceptClass: "p-button-danger",
        blockScroll: false,
        accept: () => {
          RegisterService.delete(id)
            .then((res) => {
              if (res.data.success) {
                this.$notify(
                  {
                    group: "bottom",
                    title: "Success",
                    text: res.data?.message,
                  },
                  4000
                );
                this.$router.push({ name: "ClaimHSRegisterIndex" });
              }
            })
            .catch((err) => {
              this.$notify(
                {
                  group: "bottom",
                  title: "Error",
                  text: err?.response?.data?.message,
                },
                4000
              );
            });
        },
      });
    },
    getDetail() {
      RegisterService.detail(this.id)
        .then((res) => {
          this.formValues = res.data;
        })
        .catch((err) => {
          this.$notify(
            {
              group: "bottom",
              title: "Error",
              text: err?.response?.data?.message,
            },
            4000
          );
        });
    },
    formatDate(date) {
      if (!date) return "";
      return moment(date).format("DD/MM/YYYY");
    },
    formatCurrency(number) {
      if (!number) return "";
      if (typeof number === "string") {
        number = parseFloat(number);
      }
      return number.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    },
  },
  mounted() {
    this.getDetail();
  },
};
</script>
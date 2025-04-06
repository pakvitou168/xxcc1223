<template>
  <div>
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div
            class="text-theme-1 font-semibold text-3xl text-center uppercase"
          >
            <span>{{ product_insurance_name }}</span>
          </div>
          <div class="mt-2 text-xl text-center">POLICY SCHEDULE</div>
        </div>
        <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
          <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
            <div
              class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2"
            >
              Policy No.: <span>{{ documentNo }}</span>
            </div>
            <div
              class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2"
            >
              Business Code: {{ formValues.business_code }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">THE INSURED NAME:</div>
          </div>
          <div class="w-2/3">
            <!-- <div class="text-md font-bold mb-3">ABC Company</div> -->
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.insured_name }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">CORRESPONDENCE ADDRESS:</div>
          </div>
          <div class="w-2/3">
            <!-- <div class="text-md font-medium mb-3">No. 27DEF, Preah Monivong Blvd, Sangkat Srah Chork, Khan Daun Penh, Phnom Penh, Kingdom of Cambodia.</div> -->
            <div class="text-md font-medium mb-3">
              <span>{{ customer_address }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">BUSINESS / OCCUPATION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ formValues.customer_classification }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ effectivePeriod }} - {{ periodOfInsurance }}
            </div>
          </div>
        </div>
        <div class="flex" v-if="loadedVehicle">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">COVERAGE:</div>
          </div>
          <div class="w-2/3">
            <div v-for="item in coverage" :key="item.code">
              <div class="text-sm font-bold mb-1">
                {{ item.name }} ({{ item.code }})
              </div>
              <div class="text-sm mb-2" v-html="item.html_detail"></div>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">POLICY WORDING:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm">
              Subject to {{ product_insurance_name }} Policy Wording ({{
                formValues.policy_wording_version
              }})
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16">
        <div
          class="flex"
          v-if="loadedVehicle && formValues.vehicles.length > 1"
        >
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">INSURED VEHICLE:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-sm mb-3 underline cursor-pointer btn-attach"
              @click="exportVehicles"
            >
              {{ formValues.vehicles.length + " units as per list attached" }}
            </div>
          </div>
        </div>
        <div
          class="flex"
          v-if="loadedVehicle && formValues.vehicles.length > 1"
        >
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">TOTAL SUM INSURED:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm font-bold mb-3">
              {{ formValues.sum_insured   }}
            </div>
          </div>
        </div>
        <div
          class="flex"
          v-if="loadedVehicle && formValues.vehicles.length > 1"
        >
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">TOTAL PREMIUM:</div>
          </div>
          <div class="w-2/3">
            <div class="text-sm font-bold mb-3">
              {{ formValues.total_premium   }}
            </div>
          </div>
        </div>
        <div
          class="table-responsive"
          v-if="loadedVehicle && formValues.vehicles.length == 1"
        >
          <table class="table">
            <thead>
              <tr>
                <th class="border-b-2 whitespace-nowrap">Make and Model</th>
                <th class="border-b-2 whitespace-nowrap">Plate No.</th>
                <th class="border-b-2 whitespace-nowrap">Chassis No.</th>
                <th class="border-b-2 whitespace-nowrap">Engine No.</th>
                <th class="border-b-2 whitespace-nowrap">
                  Year of Manufacture
                </th>
                <th
                  v-if="isCommercialVehicle"
                  class="border-b-2 whitespace-nowrap"
                >
                  Seats/Tonnage
                </th>
                <th
                  v-if="!isCommercialVehicle"
                  class="border-b-2 whitespace-nowrap"
                >
                  Cubic Capacity/Engine Power
                </th>
                <th class="border-b-2 whitespace-nowrap">Sum Insured (USD)</th>
                <th class="border-b-2 whitespace-nowrap">Premium (USD)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="vehicle in formValues.vehicles" :key="vehicle.id">
                <td class="border-b">
                  {{ vehicle.make_name }} {{ vehicle.model_name }}
                </td>
                <td class="border-b">{{ vehicle.plate_no }}</td>
                <td class="border-b">{{ vehicle.chassis_no }}</td>
                <td class="border-b">{{ vehicle.engine_no }}</td>
                <td class="border-b">{{ vehicle.manufacturing_year }}</td>
                <td v-if="isCommercialVehicle" class="border-b">
                  {{ vehicle.passenger_tonnage }}
                </td>
                <td v-if="!isCommercialVehicle" class="border-b">
                  {{ vehicle.cubic }}
                </td>
                <td class="border-b">{{ vehicle.vehicle_value   }}</td>
                <td class="border-b">{{ vehicle.premium   }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">LIMITATION AS TO USE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ limitToUse }}</div>
          </div>
        </div>
        <div v-if="loadedVehicle">
          <div
            v-if="
              formValues.vehicles[0].discount && formValues.vehicles.length == 1
            "
            class="flex"
          >
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">DISCOUNT:</div>
            </div>
            <div class="w-2/3">
              <div class="text-md font-medium mb-3">{{ discount }}</div>
            </div>
          </div>

          <div class="flex">
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">NO CLAIM DISCOUNT:</div>
            </div>
            <div class="w-2/3" v-if="formValues.vehicles.length == 1">
              <div class="text-md font-medium mb-3">{{ ncd }}</div>
            </div>
            <div class="w-2/3" v-else-if="formValues.vehicles.length > 1">
              <div class="text-sm font-bold mb-2">As per list attached</div>
            </div>
          </div>
        </div>

        <div class="flex" v-if="loadedVehicle">
          <div class="w-1/3">
            <div class="text-sm font-bold mb-3">DEDUCTIBLE:</div>
          </div>
          <div class="w-2/3" v-if="formValues.vehicles.length == 1">
            <div class="text-sm font-bold mb-2">
              It is applicable to each and every claim for:
            </div>
            <div
              class="text-sm mb-2"
              v-for="item in deductibles"
              :key="item.comp_code"
            >
              {{ item.cover ? item.cover.deductible_label : "" }}:
              {{ item.value }}
            </div>
          </div>
          <div class="w-2/3" v-else-if="formValues.vehicles.length > 1">
            <div class="text-sm font-bold mb-2">As per list attached</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ENDORSEMENTS/CLAUSES:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-for="endorsement in formValues.endorsement_clause"
              :key="endorsement.id"
            >
              {{ endorsement.clause }}
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">GENERAL EXCLUSIONS:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-for="exclusion in formValues.general_exclusive"
              :key="exclusion.id"
            >
              {{ exclusion.clause }}
            </div>
          </div>
        </div>
        <div v-if="formValues.warranty" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">WARRANTY:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-html="formValues.warranty"
            ></div>
          </div>
        </div>
        <div v-if="formValues.memorandum" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">MEMORANDUM:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-html="formValues.memorandum"
            ></div>
          </div>
        </div>
        <div v-if="formValues.subjectivity" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">SUBJECTIVITY:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-html="formValues.subjectivity"
            ></div>
          </div>
        </div>
        <div v-if="formValues.remark" class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">REMARK:</div>
          </div>
          <div class="w-2/3">
            <div
              class="text-md font-medium mb-3"
              v-html="formValues.remark"
            ></div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">JURISDICTION:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">Kingdom of Cambodia</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED ON:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">{{ issuedDate }}</div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ISSUED BY:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ formValues.issued_by }}
            </div>
          </div>
        </div>
      </div>
      <div class="px-5 sm:px-16 py-5">
        <div class="flex">
          <div class="w-auto">
            <div class="text-md font-bold mb-3 uppercase">
              Phillip General Insurance (Cambodia) Plc. {{ isEPolicy }}
            </div>
            <div
              class="my-2 block"
              v-bind:class="{ relative: signature }"
              style="min-height: 150px"
            >
              <img
                v-if="(signature || isEPolicy) && canShowSignature"
                class="object-cover"
                :src="(isEPolicy ? '/images/stamp/epolicy_signature.png' : ('/g' + signature.file_url))"
                style="max-height: 150px"
              />
              <img
                v-if="signature && canShowSignature"
                class="img-under"
                src="/images/stamp/phillip_insurance.png"
                style="max-height: 150px"
              />
            </div>

            <hr class="my-3" />

            <div class="text-md mb-3 font-medium">Authorised Signature</div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="commissionData" class="intro-y box overflow-hidden mt-5">
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
    <div v-if="hasReinsuranceDataYet" class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div
            class="text-theme-1 font-semibold text-3xl text-center uppercase"
          >
            POLICY REINSURANCE
          </div>
        </div>
      </div>
      <div
        v-for="(reinsurance, detailId, index) in reinsuranceData"
        :key="detailId"
        class="pt-5 pb-2 first:pt-0 px-5 sm:px-16 py-5"
      >
        <ReinsuranceItem
          v-if="policyId"
          :id="policyId"
          :detailId="detailId"
          :index="index"
          :reinsurance="reinsurance"
          :readOnly="true"
          @toggle="toggleReinsurance"
          @collapse="collapseReinsurance"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";
import ReinsuranceItem from "../../Reinsurance/ReinsuranceItem.vue";
import ComissionItem from '../../Commission/ComissionItem.vue';
import LoadingIndicator from "../../../../../components/LoadingIndicator.vue";

export default {
  components: {
    ReinsuranceItem,
    ComissionItem,
    LoadingIndicator
  },
  props: {
    id: Number,
    documentNo: String,
    policyId: [Number, String],
    policyStatus: String,
  },

  data() {
    return {
      functionCode: "AUTO",
      businessName: "",
      formValues: {},
      coverage: [],
      deductibles: [],
      loadedVehicle: false,
      signature: null,
      approveDialog: false,
      acceptDialog: false,
      submitted: false,
      reinsuranceData: {},
      commissionData: null,
      loading:false
    };
  },

  computed: {
    customer_insured() {
      return this.formValues.customer ? this.formValues.customer.name_en : null;
    },
    customer_address() {
        return this.formValues.customer ? this.handleCustomerAddress(this.formValues.customer.address_en, this.formValues.customer.village_en, this.formValues.addressData, this.formValues.country) : null
    },
    product_insurance_name() {
      return this.formValues.product ? this.formValues.product.name : null;
    },
    issuedDate() {
      var date = this.formValues.updated_at ?? this.formValues.created_at;
      return moment(date).format("DD/MM/YYYY");
    },

    ncd() {
      return `${this.formValues.vehicles[0].ncd ?? 0} %`;
    },
    discount() {
      return `${this.formValues.vehicles[0].discount} %`;
    },
    isCommercialVehicle() {
      return this.hasPassengerOrTonnage
    },
    hasPassengerOrTonnage() {
      return this.formValues.has_passenger_tonnage
    },
    periodOfInsurance() {
      if (
        this.formValues.effective_date_from &&
        this.formValues.effective_date_to
      ) {
        return `From ${moment(this.formValues.effective_date_from).format(
          "DD/MM/YYYY"
        )} To ${moment(this.formValues.effective_date_to).format(
          "DD/MM/YYYY"
        )} (Both days inclusive)`;
      }
      return "";
    },
    hasReinsuranceDataYet() {
      return Object.keys(this.reinsuranceData).length !== 0;
    },
    canShowSignature() {
      // If policy is approved
      return this.policyStatus === "APV";
    },
    effectivePeriod() {
      return `${this.formValues.effective_day} days`
    },
    limitToUse() {
      return this.formValues.product?.limitation_to_use_en;
    },
    dataId() {
        return this.commissionData[0].data_id
    },
    isEPolicy(){
      return this.formValues.quotation === null
    }
  },

  methods: {
    resolveAuto() {
      if (this.id) {
        this.loading = true
        axios.get("/autos/show-detail/" + this.id).then((response) => {
          if (response) {
            this.formValues = response.data.auto;
            this.coverage = response.data.coverage;
            this.deductibles = response.data.deductibles;
            this.loadedVehicle = true;
            if (this.formValues.quotation)
              this.signature = response.data.signature;
            else {
              axios
                .get("/policy-service/get-signature/" + this.policyId)
                .then((response) => {
                  if (response) this.signature = response.data.signature;
                });
            }
          }
        }).finally(() => this.loading = false);
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
          axios
            .delete(`/autos/${id}`)
            .then((response) => {
              if (response.data.success) {
                this.toastMessage(response.data.message, "Success");
                this.$router.push("/quotation/autos");
              }
            })
            .catch((err) => {
              this.toastMessage("Error", "Error");
            });
        },
      });
    },

    getReinsuranceData() {
      if (this.id)
        axios
          .get(`/policy-service/get-reinsurance-data/${this.policyId}`)
          .then((response) => (this.reinsuranceData = response.data));
    },
    getCommissionData() {
      if (this.id)
        axios
          .get(`/policy-service/get-commission-data/${this.policyId}`)
          .then((response) => (this.commissionData = response.data))
          .then(() => this.getBusinessNameByBusinessCode(this.commissionData[0].business_code))
    },
    getBusinessNameByBusinessCode(businessCode) {
        axios.get(`/policy-service/get-business-name-by-business-code/${businessCode}`).then(response => {
            this.businessName = response.data
        })
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
        for (let index = 0; index < _.size(this.reinsuranceData); index++) {
            var header = document.querySelector(".show-reinsurance-" + index);
            var icon = document.querySelector(".reinsurance-icon-" + index);
            if (vehicleNo !== index) {
                header.classList.add("collapse");
                icon.classList.add("rotate", "icon-color");
                continue;
            }
            header.classList.toggle("collapse");
            icon.classList.toggle("rotate");
            icon.classList.toggle("icon-color");
        }
    },

    collapseReinsurance(vehicleNo) {
        var classId = document.querySelector(".show-reinsurance-" + vehicleNo);
        var iconId = document.querySelector(".reinsurance-icon-" + vehicleNo);
        classId.classList.remove("collapse");
        iconId.classList.remove("rotate");
        iconId.classList.remove("icon-color");
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

    toastMessage(msg, type, position = "bottom") {
      this.$notify(
        {
          group: position,
          title: type,
          text: msg,
        },
        4000
      );
    },

    exportVehicles() {
      location.href =
        "/auto-service/" + this.id + "/export-vehicles/" + this.documentNo;
    },
  },
  mounted() {
    this.resolveAuto();
    this.getReinsuranceData();
    this.getCommissionData();
  },
};
</script>

<style scoped>
.table th {
  white-space: normal;
}
.btn-attach:hover {
  color: rgb(28, 63, 170);
}
.table td,
.table th {
  padding: 0.5rem 0.75rem;
}
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

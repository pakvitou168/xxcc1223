<template>
  <div>
    <div class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            {{ product_insurance_name }}
          </div>
          <div class="mt-2 text-xl text-center uppercase"><span>ENDORSEMENT</span></div>
        </div>
        <div class="flex flex-col lg:flex-row px-5 sm:px-16 pt-5">
          <div class="text-right mt-10 lg:mt-0 lg:ml-auto">
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
              Policy & Endorsement No.: <span>{{ documentNo }}</span>
            </div>
            <div class="text-md font-medium text-theme-1 dark:text-theme-10 mt-2">
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
            <div class="text-md font-bold mb-3">
              <span>{{ formValues.insured_name }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">ADDRESS:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              <span>{{ formValues.correspondence_address }}</span>
            </div>
          </div>
        </div>
        <div class="flex">
          <div class="w-1/3">
            <div class="text-md font-bold mb-3">PERIOD OF INSURANCE:</div>
          </div>
          <div class="w-2/3">
            <div class="text-md font-medium mb-3">
              {{ formValues.period_of_insurance }}
            </div>
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
            <div class="text-sm mb-3" v-html="endorsementDesc"></div>
          </div>
        </div>
        <div class="flex">
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">ENDORSEMENT TOTAL PREMIUM (USD):</div>
            </div>
            <div class="w-2/3">
              <div class="text-md font-medium mb-3">
                {{ formValues.endorsement_premium }}  
              </div> 
            </div>
        </div>
        <div class="flex">
            <div class="w-1/3">
              <div class="text-md font-bold mb-3">ISSUED ON:</div>
            </div>
            <div class="w-2/3">
              <div class="text-md font-medium mb-3">{{ formValues.issued_on }}</div>
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
            <div class="text-md font-bold mb-3 uppercase">
              Phillip General Insurance (Cambodia) Plc.
            </div>
            <div
              class="my-2"
              v-bind:class="{ relative: signature }"
              style="min-height: 150px"
            >
              <img
                v-if="signature && canShowSignature"
                class="img-over"
                :src="'/' + signature.file_url"
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
    <div v-if="hasCommissionDataYet" class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            Commission
          </div>
        </div>
      </div>
<!--      <div-->
<!--        v-for="(commission, index) in commissionData"-->
<!--        :key="index"-->
<!--        class="pt-5 pb-2 first:pt-0 px-5 sm:px-16 py-5"-->
<!--      >-->
<!--        <CommissionItem-->
<!--          :id="id"-->
<!--          :index="index"-->
<!--          :commission="commission"-->
<!--          :dataId="dataId"-->
<!--          @toggle="toggleCommission"-->
<!--          @collapse="collapseCommission"-->
<!--          :businessName="businessName"-->
<!--        />-->
<!--      </div>-->
    </div>
    <TravelReInsurance
        v-if="hasReinsuranceDataYet && id && formValues.data_id"
        :dataId="formValues.data_id"
        :id="id"
        :productCode="formValues.product_code"
      />
    <!-- <div v-if="hasReinsuranceDataYet" class="intro-y box overflow-hidden mt-5">
      <div class="text-center">
        <div class="pt-10">
          <div class="text-theme-1 font-semibold text-3xl text-center uppercase">
            REINSURANCE
          </div>
        </div>
      </div>
      <div class="pt-5 pb-2 first:pt-0 px-5 sm:px-16 py-5">
        <ReinsuranceItem
          v-if="id"
          :id="id"
          :reinsurance="reinsuranceData"
          :readOnly="true"
          :isEndorsement="true"
          :endorsementNo="documentNo"
          @toggle="toggleReinsurance"
          @collapse="collapseReinsurance"
        />
      </div>
    </div> -->
  </div>
</template>

<script>
import ReinsuranceItem from "../../../../Policy/Travel/FormTabs/Policy/Reinsurance/ReinsuranceItem.vue";
import CommissionItem from "../../Commission/Travel/CommissionItem.vue";
import endorsementService from "@/services/travel/policy/endorsement.service";
import policyService from "@/services/travel/policy/policy_service.service";
import endorsementServiceService from "@/services/travel/policy/endorsement_service.service";
import TravelReInsurance from "@/views/Policy/Travel/FormTabs/Policy/Reinsurance/ReInsurance.vue"

export default {
  components: {
    ReinsuranceItem,
    CommissionItem: CommissionItem,
    TravelReInsurance:TravelReInsurance
  },
  props: {
    id: [Number, String],
    documentNo: String,
    endorsementId: [Number, String],
    endorsementStatus: String,
  },

  data() {
    return {
      businessName: "",
      formValues: {},
      signature: null,
      endorsementType: "",
      endorsementDesc: "",
      endorsementPremium: "",
      reinsuranceData: [],
      commissionData: null,
      loadedNameList: false,
    };
  },

  computed: {
    product_insurance_name() {
      return this.formValues.product ? this.formValues.product.name : null;
    },
    endorsementEffectiveDate() {
      return moment(this.formValues.endorsement_e_date).format("DD/MM/YYYY");
    },
    hasCommissionDataYet() {
      return !_.isEmpty(this.commissionData);
    },
    hasReinsuranceDataYet() {
      return Object.keys(this.reinsuranceData).length !== 0;
    },
    canShowSignature() {
      // If endorsement is approved
      return this.endorsementStatus === "APV";
    },
    dataId() {
      return this.commissionData[0].data_id;
    },
  },

  methods: {
    getEndorsement() {
      if (this.id) {
        endorsementService.showDetail(this.id).then((response) => {
          if (response) {
            this.formValues = response.data.hs;
            this.endorsementType = response.data.endorsement_type;
            this.endorsementDesc = response.data.endorsement_description;
            this.loadedNameList = true;
          }
        })
        .then(() => this.getRawEndorsementPremium(this.documentNo));
      }
    },
    getReinsuranceData() {
      if (this.id)
        endorsementServiceService
          .getReinsuranceData(this.id)
          .then((response) => (this.reinsuranceData = response.data));
    },
    getCommissionData() {
      if (this.id)
        endorsementServiceService
          .getCommissionData(this.id)
          .then((response) => (this.commissionData = response.data))
          .then(() => {
            if (!_.isEmpty(this.commissionData))
              this.getBusinessNameByBusinessCode(this.commissionData[0].business_code);
          });
    },
    getBusinessNameByBusinessCode(businessCode) {
      policyService.getBusinessNameByBusinessCode(businessCode).then((response) => {
        this.businessName = response.data;
      });
    },
    getRawEndorsementPremium(documentNo) {
        axios.get(`/hs/endorsements/${this.id}/get-premium/${documentNo}/1`).then(response => {
            this.$emit('get-total-premium', response.data);
        })
    },

    toggleCommission(vehicleNo) {
      for (let index = 0; index < _.size(this.commissionData); index++) {
        var header = document.querySelector(".show-commission-" + index);
        var icon = document.querySelector(".commission-icon-" + index);
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

    collapseCommission(vehicleNo) {
      var classId = document.querySelector(".show-commission-" + vehicleNo);
      var iconId = document.querySelector(".commission-icon-" + vehicleNo);
      classId.classList.remove("collapse");
      iconId.classList.remove("rotate");
      iconId.classList.remove("icon-color");
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
    exportInsuredPersons() {
      location.href = `/hs/endorsement-services/${this.formValues.data_id}/export-insured-persons/${this.formValues.policy_no}`;
    },
    someAdditionalBenefitsBiggerThanZero(key) {
      const benefits = this.formValues.additional_benefits?.some(
        (benefit) => parseFloat(benefit[key]) > 0
      );
      return benefits;
    },
  },

  mounted() {
    this.getEndorsement();
    this.getReinsuranceData();
    this.getCommissionData();
  },
};
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

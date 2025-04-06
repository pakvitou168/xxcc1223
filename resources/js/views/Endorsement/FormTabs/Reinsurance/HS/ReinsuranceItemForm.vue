<template>
  <div>
    <div v-if="editable" class="grid grid-cols-5 gap-x-2">
      <FormulateInput
        type="select"
        name="group_code"
        label="Partner Group *"
        :options="partnerGroupOptions"
        placeholder="Partner Group"
        validation="required"
        validationName="Partner Group"
        @input="updateActiveParticipantOptions"
      />
      <FormulateInput
        v-model="treaty_code"
        type="select"
        name="treaty_code"
        label="Participant *"
        placeholder="Participant"
        validation="required"
        validationName="Participant"
        :options="activeParticipantsOptions"
      />

      <FormulateInput
        type="text"
        name="share"
        label="Share (%) *"
        placeholder="Share (%)"
        validation="required"
        validationName="Share"
        v-currency="{ precision: 1, currency: null }"
      />

      <FormulateInput
        type="text"
        name="ri_commission"
        label="RI Commission (%) *"
        placeholder="RI Commission (%)"
        validation="required"
        validationName="RI Commission"
        v-currency="{ precision: 1, currency: null }"
      />

      <FormulateInput
        type="text"
        name="tax_fee"
        label="Tax & Fee (%) *"
        placeholder="Tax & Fee (%)"
        validation="required"
        validationName="Tax & Fee"
        v-currency="{ precision: 1, currency: null }"
      />
    </div>

    <div v-else class="grid grid-cols-11 gap-x-2 auto-cols-min">
      <div class="self-center">
        <div v-if="!isChild" class="self-center font-bold">
          <div class="formulate-input-element">
            {{ reinsuranceDetail.reinsurance_type }}
          </div>
        </div>
      </div>
      <div class="col-span-2 self-center">
        <div
          :class="[{ 'ml-5': isChild }, { 'font-bold': !isChild }]"
          class="self-center"
        >
          <div class="formulate-input-element">{{ participant }}</div>
        </div>
      </div>
      <div :class="[{ 'font-bold': !isChild }]">
        <div v-if="isChild" class="formulate-input-element">
          {{ reinsuranceDetail.share }}
        </div>
        <FormulateInput
          v-else
          type="text"
          name="share"
          placeholder="Share (%)"
          class="mb-0"
          v-currency="{ precision: 1, currency: null }"
          :disabled="readOnly"
        />
      </div>

      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div class="formulate-input-element">
          {{ reinsuranceDetail.premium   }}
        </div>
      </div>
      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div v-if="isChild" class="formulate-input-element">
          {{ reinsuranceDetail.ri_commission }}
        </div>
        <FormulateInput
          v-else
          type="text"
          name="ri_commission"
          placeholder="RI Commission (%)"
          class="mb-0"
          v-currency="{ precision: 1, currency: null }"
          :disabled="readOnly"
        />
      </div>

      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div class="formulate-input-element">
          {{ reinsuranceDetail.ri_commission_amt   }}
        </div>
      </div>

      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div v-if="isChild" class="formulate-input-element">
          {{ reinsuranceDetail.tax_fee }}
        </div>
        <FormulateInput
          v-else
          type="text"
          name="tax_fee"
          placeholder="Tax & Fee (%)"
          class="mb-0"
          v-currency="{ precision: 1, currency: null }"
          :disabled="readOnly"
        />
      </div>

      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div class="formulate-input-element">
          {{ reinsuranceDetail.tax_fee_amt   }}
        </div>
      </div>

      <div :class="[{ 'font-bold': !isChild }]" class="self-center">
        <div class="grid grid-cols-2 auto-cols-min">
          <div class="formulate-input-element">
            {{ reinsuranceDetail.net_premium   }}
          </div>
          <div
            v-if="canDeleteReinsurance"
            class="place-self-end"
            @mouseover="hover = true"
            @mouseleave="hover = false"
          >
            <span v-if="isPendingDelete" class="text-red-600"
              >Pending Delete</span
            >
            <a v-else @click="deleteReinsurance" :id="reinsuranceDetail.id">
              <svg
                v-if="hover"
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="red"
                viewBox="0 0 24 24"
                stroke="white"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div v-if="hasChild">
      <FormulateInput
        v-model="reinsuranceDetail.sub_reinsurance_data"
        type="group"
        :repeatable="false"
        group-repeatable-class="formulate-input-group-repeatable relative py-2"
        outer-class="formulate-input mb-0"
        #default="{ index }"
      >
        <reinsurance-item-form
          v-if="reinsuranceDetail.sub_reinsurance_data"
          :reinsuranceDetail="reinsuranceDetail.sub_reinsurance_data[index]"
        />
      </FormulateInput>
    </div>
  </div>
</template>
  
  <script>
export default {
  name: "reinsurance-item-form",
  props: {
    reinsuranceDetail: Object,
    editable: {
      type: Boolean,
      default: false,
    },
    readOnly:{
      type: Boolean,
      default: false,
    },
    participantOptions: Array,
    partnerGroupOptions: Object,
    defaultPartnerGroups: Array,
  },

  data() {
    return {
      activeParticipantsOptions: {},
      treaty_code: null,
      hover: false,
      isPendingDelete: false,
    };
  },
  computed: {
    hasChild() {
      if (!this.reinsuranceDetail.sub_reinsurance_data) return false;
      return this.reinsuranceDetail.sub_reinsurance_data.length > 0;
    },

    isChild() {
      return this.reinsuranceDetail?.parent_code;
    },

    participant() {
      return (
        this.reinsuranceDetail.participant ?? this.reinsuranceDetail.treaty_code
      );
    },

    canDeleteReinsurance() {
      return !this.defaultPartnerGroups?.includes(
        this.reinsuranceDetail.treaty_code
      );
    },
  },
  methods: {
    updateActiveParticipantOptions(selectedPartnerGroup) {
      let activeParticipantsOptions = [];
      // set treaty code to null if users change partner group
      this.treaty_code = null;
      for (let participantOption of this.participantOptions) {
        if (participantOption.group_code == selectedPartnerGroup) {
          let activeParticipants = {
            value: participantOption.code,
            label: participantOption.name,
          };
          activeParticipantsOptions.push(activeParticipants);
        }
      }
      this.activeParticipantsOptions = activeParticipantsOptions;
    },
    deleteReinsurance() {
      this.isPendingDelete = true;
      this.$emit("getReinsurancePendingDeleteId", this.reinsuranceDetail.id);
    },
  },
  mounted() {
  },
};
</script>
  
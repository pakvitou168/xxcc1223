<template>
  <div class="p-4  border-l border-r border-b border-gray-200 ">
    <div v-if="editable" class="grid grid-cols-5 gap-x-2 items-center">
      <div>
        <label class=" block mb-1" for="">Partner Group *</label>
        <Dropdown
          v-model="formData.reinsurance_type"
          class="w-full border-0"
          optionLabel="label"
          optionValue="value"
          :options="formattedPartnerGroupOptions"
          :filter="true"
          :showClear="true"
          @change="updateActiveParticipantOptions"
          placeholder="Select Partner Group"
          :required="true"
          :class="['w-full p-inputtext', {'': errors.partner_group}]"
        />
        <div class="h-6">
          <small v-if="errors.partner_group" class="p-error block mt-1">{{ errors.partner_group[0] }}</small>
        </div>
      </div>
      <div>
        <label class=" block mb-1" for="">Participant *</label>
        <Dropdown
          v-model="formData.treaty_code"
          class="w-full"
          optionLabel="label"
          optionValue="value"
          :options="activeParticipantsOptions"
          :filter="true"
          :showClear="true"
          placeholder="Participant"
          :required="true"
          :class="['w-full p-inputtext', {'': errors.treaty_code}]"
        />
        <div class="h-6">
          <small v-if="errors.treaty_code" class="p-error block mt-1">{{ errors.treaty_code[0] }}</small>
        </div>
      </div>

      <div>
        <label for="share" class=" block mb-1">Share (%) *</label>
        <InputNumber
          v-model="formData.share"
          id="share"
          class="w-full p-0"
          placeholder="Share (%)"
          :minFractionDigits="1"
          :maxFractionDigits="1"
          :required="true"
          :class="[' p-inputtext ', {'': errors.share}]"
        />
        <div class="h-6">
          <small v-if="errors.share" class="p-error block mt-1">{{ errors.share[0] }}</small>
        </div>
      </div>
      <div>
        <label for="ri_commission" class=" block mb-1">RI Commission (%) *</label>
        <InputNumber
          v-model="formData.ri_commission"
          id="ri_commission"
          class="w-full p-0"
          placeholder="RI Commission (%)"
          :minFractionDigits="1"
          :maxFractionDigits="1"
          :required="true"
          :class="[' p-inputtext ', {' ': errors.ri_commission}]"
        />
        <div class="h-6">
          <small v-if="errors.ri_commission" class="p-error block mt-1">{{ errors.ri_commission[0] }}</small>
        </div>
      </div>

      <div class="relative">
        <label for="tax_fee" class=" block mb-1">Tax & Fee (%) *</label>
        <div class="flex items-center p-0">
          <InputNumber
            v-model="reinsuranceDetail.tax_fee"
            id="tax_fee"
            class="w-full p-0"
            placeholder="Tax & Fee (%)"
            :minFractionDigits="1"
            :maxFractionDigits="1"
            :required="true"
            :class="['p-inputtext', {'': errors.tax_fee}]"
          />
          <div class="ml-2 flex items-center align-middle">
            <a @click="removeRow" class=" hover:bg-gray-100 rounded-full cursor-pointer" @mouseover="hover = true"
               @mouseleave="hover = false">
              <svg v-if="hover" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="red" viewBox="0 0 24 24"
                   stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </a>
          </div>
        </div>
        <div class="h-6">
          <small v-if="errors.tax_fee" class="p-error block mt-1">{{ errors.tax_fee[0] }}</small>
        </div>
      </div>

    </div>

    <div v-else class="grid grid-cols-10 gap-x-2 auto-cols-min">
      <div class="self-center">
        <div v-if="!isChild" class="self-center ">
          <div class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.reinsurance_type }}</div>
        </div>
      </div>
      <div class="col-span-2 self-center">
        <div :class="[{'ml-5': isChild}, {'': !isChild}]" class="self-center">
          <div class="p-2 rounded border border-gray-300">{{ participant }}</div>
        </div>
      </div>
      <div :class="[{'': !isChild}]">
        <div v-if="isChild" class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.share }}</div>
        <div v-else class="mb-0">
          <InputText
            v-model="reinsuranceDetail.share"
            class="w-full mb-0"
            placeholder="Share (%)"
            :minFractionDigits="1"
            :maxFractionDigits="1"
            :disabled="isPendingDelete"
          />
        </div>
      </div>

      <div :class="[{'': !isChild}]" class="self-center">
        <div class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.premium }}</div>
      </div>
      <div :class="[{'': !isChild}]" class="self-center">
        <div v-if="isChild" class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.ri_commission }}</div>
        <div v-else class="mb-0">
          <InputText
            v-model="reinsuranceDetail.ri_commission"
            class="w-full mb-0"
            placeholder="RI Commission (%)"
            :minFractionDigits="1"
            :maxFractionDigits="1"
            :disabled="isPendingDelete"
          />
        </div>
      </div>

      <div :class="[{'': !isChild}]" class="self-center">
        <div class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.ri_commission_amt }}</div>
      </div>

      <div :class="[{'': !isChild}]" class="self-center">
        <div v-if="isChild" class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.tax_fee }}</div>
        <div v-else class="w-full mb-0">
          <InputText
            v-model="reinsuranceDetail.tax_fee"
            class="w-full mb-0"
            placeholder="Tax & Fee (%)"
            :minFractionDigits="1"
            :maxFractionDigits="1"
            :disabled="isPendingDelete"
          />
        </div>
      </div>

      <div :class="[{'': !isChild}]" class="self-center">
        <div class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.tax_fee_amt }}</div>
      </div>

      <div :class="[{'': !isChild}]" class="self-center">
        <div class="grid grid-cols-2 auto-cols-min">
          <div class="p-2 rounded border border-gray-300">{{ reinsuranceDetail.net_premium }}</div>
          <div v-if="canDeleteReinsurance" class="place-self-end" @mouseover="hover = true" @mouseleave="hover = false">
            <span v-if="isPendingDelete" class="text-red-600">Pending Delete</span>
            <a v-else @click="deleteReinsurance" :id="reinsuranceDetail.id">
              <svg v-if="hover" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="red" viewBox="0 0 24 24"
                   stroke="white" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                   stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div v-if="hasChild" class="relative py-2">
      <div v-if="reinsuranceDetail.sub_reinsurance_data && reinsuranceDetail.sub_reinsurance_data.length">
        <reinsurance-item-form
          v-for="(subData, index) in reinsuranceDetail.sub_reinsurance_data"
          :key="index"
          :reinsuranceDetail="subData"
          :participantOptions="participantOptions"
          :partnerGroupOptions="partnerGroupOptions"
          :defaultPartnerGroups="defaultPartnerGroups"
          @update:subData="updateSubData(index, $event)"
        />
      </div>
    </div>

  </div>
</template>

<script>
import CustomInputNumber from "@/views/Policy/FormTabs/HS/CustomInputNumber.vue";

export default {
  name: "reinsurance-item-form",
  components: {CustomInputNumber},
  props: {
    reinsuranceDetail: Object,
    editable: {
      type: Boolean,
      default: false,
    },
    participantOptions: Array,
    partnerGroupOptions: Object,
    defaultPartnerGroups: Array,
    errors: {
      type: Object,
      default: () => ({})
    },
    index: {
      type: [String, Number],
    }
  },

  data() {

    return {
      ERROR_MESSAGE: "Something went wrong!",
      SUCCESS_MESSAGE: "Success!",
      activeParticipantsOptions: [],
      formData: {
        partner_group: null,
        treaty_code: null,
        share: null,
        ri_commission: null,
        tax_fee: null
      },
      treaty_code: null,
      hover: false,
      isPendingDelete: false,
    };
  },
  computed: {
    formattedPartnerGroupOptions() {
      return Object.entries(this.partnerGroupOptions).map(([value, label]) => ({
        value,
        label
      }))
    },
    hasChild() {
      if (!this.reinsuranceDetail.sub_reinsurance_data) return false;
      return this.reinsuranceDetail.sub_reinsurance_data.length > 0;
    },

    isChild() {
      return this.reinsuranceDetail?.parent_code ? true : false;
    },

    participant() {
      return this.reinsuranceDetail.participant ?? this.reinsuranceDetail.treaty_code;
    },

    canDeleteReinsurance() {
      return !this.defaultPartnerGroups?.includes(this.reinsuranceDetail.treaty_code);
    },

  },
  methods: {
    resetFormData() {
      this.formData = {
        partner_group: null,
        treaty_code: null,
        share: null,
        ri_commission: null,
        tax_fee: null
      };
      this.activeParticipantsOptions = [];
    },
    removeRow() {
      this.$emit('removeRow', this.index)
    },
    emitUpdate() {
      this.$emit('update:value', {...this.formData});
    },
    updateSubData(index, data) {
      this.$set(this.formData.sub_reinsurance_data, index, data);
      this.$emit('update:value', this.formData);
    },
    updateActiveParticipantOptions(event) {
      const selectedGroup = event.value;
      let activeParticipantsOptions = [];
      // set treaty code to null if users change partner group
      this.treaty_code = null;
      // for (let participantOption of this.participantOptions) {
      //   if (participantOption.group_code == selectedPartnerGroup) {
      //     let activeParticipants = {
      //       value: participantOption.code,
      //       label: participantOption.name,
      //     };
      //     activeParticipantsOptions.push(activeParticipants);
      //   }
      // }
      // this.activeParticipantsOptions = activeParticipantsOptions;

      this.activeParticipantsOptions = this.participantOptions
        .filter(participant => participant.group_code === selectedGroup)
        .map(participant => ({
          value: participant.code,
          label: participant.name
        }));

      this.emitUpdate();

    },
    deleteReinsurance() {
      this.isPendingDelete = true;
      this.$emit("getReinsurancePendingDeleteId", this.reinsuranceDetail.id);
    },
  },

  watch: {
    'formData': {
      deep: true,
      handler(newVal) {
        this.emitUpdate();
      }
    },
    'reinsuranceDetail': {
      immediate: true,
      handler(newVal) {
        if (newVal && Object.keys(newVal).length) {
          this.formData = {...newVal};
        }
      }
    },
    'reinsuranceDetail.sub_reinsurance_data': {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.formData.sub_reinsurance_data = [...newVal];
        }
      }
    },
  },
  mount() {
    console.log('123')
  },
};
</script>

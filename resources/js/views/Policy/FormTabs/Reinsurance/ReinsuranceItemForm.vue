<template>
    <div>
        <div v-if="editable" class="grid grid-cols-5 gap-x-5 gap-y-6">
            <div>
                <label for="" class="block mb-1 text-semibold">Partner Group *</label>
                <Dropdown class="w-full" v-model="modelValue.group_code" :options="partnerGroupOptions"
                    placeholder="Select partner group" showClear @change="updateActiveParticipantOptions($event.value)"
                    optionLabel="label" optionValue="value">
                </Dropdown>
            </div>
            <div>
                <label for="" class="block mb-1 text-semibold">Participant *</label>
                <Dropdown class="w-full" v-model="modelValue.treaty_code" :options="activeParticipantsOptions"
                    placeholder="Select partner group" showClear optionLabel="label" optionValue="value">
                </Dropdown>
            </div>
            <div>
                <label for="" class="block mb-1 text-semibold">Share (%) *</label>
                <InputNumber v-model="modelValue.share" :min="0" :max="100" placeholder="Share (%)"
                    class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
            </div>
            <div>
                <label for="" class="block mb-1 text-semibold">RI Commission (%) *</label>
                <InputNumber v-model="modelValue.ri_commission" :min="0" :max="100" placeholder="RI Commission"
                    class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
            </div>
            <div class="flex justify-between items-center">
                <div class="flex-1">
                    <label for="" class="block mb-1 text-semibold">Tax & Fee (%) *</label>
                    <InputNumber v-model="modelValue.tax_fee" :min="0" :max="100" placeholder="RI Commission"
                        class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2" />
                </div>
                <div v-if="canDeleteReinsurance" @mouseover="hover = true" class="mt-4 ml-2"
                    @mouseleave="hover = false">
                    <span v-if="isPendingDelete" class="text-red-600">Pending Delete</span>
                    <a v-else @click="deleteReinsurance" :id="modelValue.id">
                        <svg v-if="hover" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="red"
                            viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div v-else class="grid grid-cols-11 gap-x-5 gap-y-10 auto-cols-min">
            <div class="self-center">
                <div v-if="!isChild" class="self-center font-bold">
                    <div>{{ reinsuranceDetail.reinsurance_type }}</div>
                </div>
            </div>
            <div class="col-span-2 self-center">
                <div :class="[{ 'ml-5 my-3': isChild }, { 'font-bold': !isChild }]" class="self-center">
                    <div>{{ participant }} </div>
                </div>
            </div>
            <div :class="[{ 'font-bold': !isChild }]">
                <div v-if="isChild || !canUpdateReinsurance" class="my-3">{{ reinsuranceDetail.share
                    }}</div>
                <div v-else>
                    <InputNumber v-model="reinsuranceDetail.share" :min="0" :max="100" placeholder="Share (%)"
                        class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2"
                        :disabled="isPendingDelete" />
                </div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div>{{ formatCurrency(reinsuranceDetail.sum_insured) }}</div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div>{{ formatCurrency(reinsuranceDetail.premium) }}</div>
            </div>
            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div v-if="isChild || !canUpdateReinsurance">{{
                    (reinsuranceDetail.ri_commission) }}</div>
                <div v-else>
                    <InputNumber v-model="reinsuranceDetail.ri_commission" :min="0" :max="100"
                        placeholder="RI Commission" class="w-full" :useGrouping="false" :minFractionDigits="0"
                        :maxFractionDigits="2" :disabled="isPendingDelete" />
                </div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div>{{ formatCurrency(reinsuranceDetail.ri_commission_amt) }}</div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div v-if="isChild || !canUpdateReinsurance">{{
                    reinsuranceDetail.tax_fee }}</div>
                <div v-else>
                    <InputNumber v-model="reinsuranceDetail.tax_fee" :min="0" :max="100" placeholder="RI Commission"
                        class="w-full" :useGrouping="false" :minFractionDigits="0" :maxFractionDigits="2"
                        :disabled="isPendingDelete" />
                </div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div>{{ formatCurrency(reinsuranceDetail.tax_fee_amt) }}</div>
            </div>

            <div :class="[{ 'font-bold': !isChild }]" class="self-center">
                <div class="grid grid-cols-2 auto-cols-min">
                    <div>{{ formatCurrency(reinsuranceDetail.net_premium) }}</div>
                    <div v-if="canDeleteReinsurance" class="place-self-end" @mouseover="hover = true"
                        @mouseleave="hover = false">
                        <span v-if="isPendingDelete" class="text-red-600">Pending Delete</span>
                        <a v-else @click="deleteReinsurance" :id="reinsuranceDetail.id">
                            <svg v-if="hover" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="red"
                                viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="hasChild">
            <reinsurance-item-form v-if="reinsuranceDetail.sub_reinsurance_data"
                v-for="(subReisnurance, subIndex) in reinsuranceDetail.sub_reinsurance_data" v-model="reinsuranceDetail.sub_reinsurance_data[subIndex]"
                :reinsuranceDetail="subReisnurance" />
        </div>

    </div>
</template>

<script>
import { formatCurrency } from '../../../../helpers';
export default {
    name: 'reinsurance-item-form',
    props: {
        reinsuranceDetail: {
            type: Object,
            default: {

            }
        },
        editable: {
            type: Boolean,
            default: false,
        },
        readOnly: {
            type: Boolean,
            default: true,
        },
        participantOptions: Array,
        isEndorsement: Boolean,
        endorsementNo: String,
        partnerGroupOptions: Object,
        defaultPartnerGroups: Array,
        rowId: Number,
        modelValue: {
            type: Object,
            default: {}
        }
    },

    data() {
        return {
            activeParticipantsOptions: [],
            treaty_code: null,
            hover: false,
            isPendingDelete: false,
        }
    },

    computed: {
        hasChild() {
            if (!this.reinsuranceDetail?.sub_reinsurance_data) return false
            return this.reinsuranceDetail?.sub_reinsurance_data.length > 0
        },

        isChild() {
            return (this.reinsuranceDetail?.parent_code) ? true : false;
        },

        participant() {
            return this.reinsuranceDetail?.participant ?? this.reinsuranceDetail?.treaty_code
        },

        canUpdateReinsurance() {
            if (this.readOnly)
                return false
            if (this.isEndorsement) {
                if (this.reinsuranceDetail.endorsement_stage)
                    return this.reinsuranceDetail.endorsement_state != 'DELETION' && this.reinsuranceDetail.endorsement_state != 'CANCEL' && this.reinsuranceDetail.endorsement_stage == this.endorsementNo
                return false
            }
            return true
        },

        canDeleteReinsurance() {
            if (this.readOnly)
                return false
            return !this.defaultPartnerGroups.includes(this.reinsuranceDetail.treaty_code)
        }
    },
    methods: {
        formatCurrency,
        updateActiveParticipantOptions(selectedPartnerGroup) {
            var activeParticipantsOptions = []
            // set treaty code to null if users change partner group
            this.treaty_code = null
            for (let index = 0; index < this.participantOptions.length; index++) {
                if (this.participantOptions[index].group_code == selectedPartnerGroup) {
                    let activeParticipants = {
                        value: this.participantOptions[index].code,
                        label: this.participantOptions[index].name
                    }
                    activeParticipantsOptions.push(activeParticipants)
                }
            }
            this.activeParticipantsOptions = activeParticipantsOptions
        },
        deleteReinsurance() {
            this.isPendingDelete = true
            if (this.reinsuranceDetail.id) this.$emit('getReinsurancePendingDeleteId', this.reinsuranceDetail.id)
            else this.$emit('removeReinsuranceDetailByRowId', this.rowId)

        }
    },
}
</script>

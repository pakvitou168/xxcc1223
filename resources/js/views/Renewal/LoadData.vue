<template>
<div class="flex-1 flex">
  <div class="flex-1"></div>
  <div class="flex gap-1">
    <div class="w-52">
      <label class="form-label">Underwriting Year *</label>
      <Calendar
        class="w-full"
        v-model="filterFields.uw_date"
        placeholder="Underwriting year"
        view="year"
        dateFormat="yy"
        :manualInput="false"
        @date-select="($event) => filterFields.uw_year = $event.getFullYear()"
      />
      <ul v-if="errors.uw_year" class="formulate-input-errors">
        <li class="formulate-input-error">{{ errors.uw_year[0] }}</li>
      </ul>
    </div>
    <div class="w-52">
      <label for="" class="form-label">From Date *</label>
      <Calendar placeholder="From date" v-model="filterFields.expired_date_from" :maxDate="filterFields.expired_date_to" />
      <ul v-if="errors.expired_date_from" class="formulate-input-errors">
        <li class="formulate-input-error">{{ errors.expired_date_from[0] }}</li>
      </ul>
    </div>
    <div class="w-52">
      <label for="" class="form-label">To Date *</label>
      <Calendar placeholder="To date" v-model="filterFields.expired_date_to" :minDate="filterFields.expired_date_from" />
      <ul v-if="errors.expired_date_to" class="formulate-input-errors">
        <li class="formulate-input-error">{{ errors.expired_date_to[0] }}</li>
      </ul>
    </div>
    <div class="pt-6">
      <button class="btn btn-primary leading-6 rounded" :class="{ 'opacity-50': loading || !canLoadData }" @click="setFilter" :disabled="loading || !canLoadData">
        <span v-if="loading">Loading...</span>
				<span v-else>Load</span>
      </button>
    </div>
  </div>
</div>
</template>

<script>
import { filterFields } from '../../store/filterFields';
export default {
  props: {
    value:Object,
		loading: Boolean,
    errors: Object,
    canLoadData: Boolean,
	},
  data() {
		return {
			underWritingDate: '',
			filterFields
		};
	},
  methods: {
		setFilter() {
			this.$emit("setFilter", this.filterFields);
		},
    selectYear (date) {
      this.value.uw_year = new Date(date)
    }
  },
}
</script>
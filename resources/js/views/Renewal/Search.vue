<template>
	<div class="flex gap-x-1">
		<div class="w-52">
			<label for="" class="form-label">Search</label>
			<InputText v-model="modelValue.search" placeholder="Search" @update:modelValue="setFilter" />
		</div>
		<div class="ml-1 w-48">
			<label for="" class="form-label">Submit Status</label>
			<Dropdown v-model="modelValue.submitStatus" class="w-full" placeholder="Select an option" :options="submitStatuses" optionLabel="label" optionValue="value"
			@update:modelValue="setFilter" />
		</div>
		<div class="ml-1 w-48">
			<label for="" class="form-label">Status</label>
			<Dropdown v-model="modelValue.status" class="w-full" placeholder="Select an option" :options="statuses" optionLabel="label" optionValue="value"
			@update:modelValue="setFilter" />
		</div>
		<div class="ml-1 pt-6">
			<button class="btn leading-6 bg-blue-800 text-white rounded" @click="clearSearch">Clear</button>
		</div>
	</div>
</template>

<script>
import renewalService from '@/services/renewal/renewal.service'

export default {
	props: {
		modelValue: Object,
	},
	data() {
		return {
			statuses: [],
			submitStatuses: [],
		};
	},
	methods: {
		setFilter() {
			this.$emit("setFilter", this.modelValue);
		},
		clearSearch() {
			this.modelValue.search = ''
			this.modelValue.status = ''
			this.modelValue.submitStatus = ''
			this.$emit("setFilter", this.modelValue);
		},
		statusLovs() {
			renewalService.statusLovs().then(res => {
				this.statuses = res.data.statuses
				this.submitStatuses = res.data.submit_statuses
			})
		}
	},
	mounted() {
		this.statusLovs()
	},
};
</script>

<template>
  <div>
    <div class="flex mr-1">
      <span>
        <CaretDown :class="`deductible-icon-${index}`" />
      </span>
      <p class="input-label text-base cursor-pointer w-full" @click="toggle(index)">Vehicle #{{ index + 1 }}</p>
    </div>
    <div class="collapse" :class="`show-deductible-${index} px-8`">
      <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
        <div class="flex my-2">
          <span class="input-label w-1/3">Make</span>
          <p class="text-sm w-2/3">{{ make }}</p>
        </div>
        <div class="flex my-2">
          <span class="input-label w-1/3">Model</span>
          <p class="text-sm w-2/3">{{ model }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-1 gap-5">
        <div class="flex my-2">
          <span class="input-label w-1/3">Covers</span>
          <p class="text-sm w-2/3">{{ covers }}</p>
        </div>
        <div class="flex my-2">
          <span class="input-label w-1/3">Sum Insured</span>
          <p class="text-sm w-2/3">{{ sumInsured }}</p>
        </div>
      </div>
      <div class="w-full">
        <div class="grid grid-cols-4 gap-x-4 mb-4" v-for="(item, index) in modelValue">
          <InputText v-model="modelValue[index].deductible_label" disabled />
          <InputText v-model="modelValue[index].value" disabled />
          <div class="flex">
            <label for="" class="pt-2 w-12">Value</label>
            <InputNumber class="flex-1" v-model="modelValue[index].cond_value" :maxFractionDigits="2" />
          </div>
          <div v-if="getValueType(index) === 'PERCENTAGE'" class="flex">
            <label class="pt-2 w-16">Min Value</label>
            <InputNumber class="flex-1" v-model="modelValue[index].min_value" placeholder="Min Value" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CaretDown from '@/components/Icons/CaretDown.vue';
export default {
  components: {
    CaretDown,
  },
  props: {
    index: Number,
    detailId: [Number, String],
    modelValue: {
      type: Array,
      default: []
    },
  },
  data() {
    return {
      data: {},
    }
  },
  computed: {
    make() {
      return this.modelValue[0]?.auto_detail?.make_model?.make?.make
    },
    model() {
      return this.modelValue[0]?.auto_detail?.make_model?.model
    },
    covers() {
      let covers = this.modelValue[0]?.auto_detail?.selected_cover_pkg ?? ''
      return covers.split(',').join(', ')
    },
    sumInsured() {
      return this.modelValue[0]?.auto_detail?.vehicle_value
    },
  },
  methods: {
    toggle(index) {
      this.$emit('toggle', index)
    },
    getValueType(index) {
      return this.modelValue[index]?.cond_value_type
    },
  },
  mounted() {
    if (this.index == 0) this.$emit('collapse', this.index)
  }
}
</script>

<style scoped>
.input-label {
  display: block;
  line-height: 1.5;
  font-weight: bold;
}

.collapse {
  display: none;
}

.rotate {
  transform: rotate(180deg);
}

.icon-color {
  color: red;
}
</style>
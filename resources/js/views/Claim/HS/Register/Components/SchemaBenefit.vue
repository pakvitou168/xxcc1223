<template>
    <div>
<!--      <div v-if="true" style="background: #f5f5f5; padding: 10px; margin: 10px 0;">-->
<!--        <p>Debug Info:</p>-->
<!--        <pre>-->
<!--          {{ {-->
<!--            hasData: !!modelValue,-->
<!--            dataLength: modelValue?.length,-->
<!--            firstItem: modelValue?.[0]-->
<!--            } }}-->
<!--        </pre>-->
<!--      </div>-->

      <template v-if="modelValue">

        <SchemaBenefitItem v-for="(schema, index) in updatedValue" :index="index" :key="index"
                           v-model="updatedValue[index]"
                           :addmissionDateErr="errors[`schema_data.${index}.admission_date`]"
                           :daysErr="errors[`schema_data.${index}.number_of_day`]"
                           :dischargeDateErr="errors[`schema_data.${index}.discharge_date`]"
                           :feePerDayErr="errors[`schema_data.${index}.fee_per_day`]" @changeExpense="changeExpense"
        />
      </template>
    </div>
</template>

<script>
import SchemaBenefitItem from './SchemaBenefitItem.vue';
export default {
    components: {
        SchemaBenefitItem
    },
    // props: {
    //     value: Array,
    //     errors:Object
    // },
    data() {
        return {
            ERROR_MESSAGE:"Something went wrong!",
            SUCCESS_MESSAGE:"Success!",
            updatedValue: this.modelValue
        }
    },
    props: {
        modelValue: Array,
        errors:Object
    },
    methods:{
      changeExpense(){
        this.$emit('changeExpense')
      }

    },
    created() {
      // Debug when component is created
      /*console.log('🏁 Child created:', {
        receivedValue: this.modelValue,
        type: typeof this.modelValue,
        isArray: Array.isArray(this.modelValue)
      });*/
    },
    mounted() {
      // Debug when component is mounted
     /* console.log('🔄 Child mounted:', {
        value: this.modelValue,
        updatedValue: this.updatedValue
      });*/
    },

    computed: {
      updatedValue: {
        get() {
          return this.modelValue;
        },
        set(newValue) {
          this.$emit('update:modelValue', newValue);  // changed from input to update:modelValue
        }
      }
    },

    updated() {
        if (this.value) {
            this.updatedValue = this.value
        }
       /* console.log('📝 Child updated:', {
          newValue: this.modelValue
        });*/
    },

}
</script>
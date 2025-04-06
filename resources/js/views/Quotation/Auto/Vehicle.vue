<template>
  <div>
    <FormulateInput
      type="hidden"
      name="id"
    />
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        type="select"
        name="make"
        validationName="Make"
        label="Make *"
        validation="required"
        placeholder="Make"
        :options="makes"
        @input="listModelsByMake"
        :disabled="!canSave"
      />

      <FormulateInput
        type="select"
        name="model"
        validationName="Model"
        label="Model *"
        validation="required"
        placeholder="Model"
        :options="models"
        @input='getModelValue'
        :disabled="!canSave"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        type="text"
        name="vehicle_value"
        validationName="Value of Vehicles"
        label="Value of Vehicles *"
        :validation="!isVehicleValueRequired ? 'required|min:0' : 'required|min:0.001'"
        step="any"
        placeholder="Value of Vehicles"
        v-currency="{currency: null}"
        :disabled="!canSave"
      />

      <FormulateInput
        type="text"
        name="cubic"
        label="Cubic Capacity/Engine Power"
        placeholder="Cubic Capacity/Engine Power"
        :disabled="!canSave"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        v-if="isVehicleInfoAvailableToEdit"
        type="text"
        name="plate_no"
        label="Plate No."
        placeholder="Plate No."
      />
      <FormulateInput
        v-else
        type="text"
        name="plate_no"
        label="Plate No."
        placeholder="Plate No."
        :disabled="!canSave"
      />

      <FormulateInput
        v-if="isVehicleInfoAvailableToEdit"
        type="text"
        name="chassis_no"
        label="Chassis No."
        placeholder="Chassis No."
      />
      <FormulateInput
        v-else
        type="text"
        name="chassis_no"
        label="Chassis No."
        placeholder="Chassis No."
        :disabled="!canSave"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        v-if="isVehicleInfoAvailableToEdit"
        type="text"
        name="engine_no"
        label="Engine No."
        placeholder="Engine No."
      />
      <FormulateInput
        v-else
        type="text"
        name="engine_no"
        label="Engine No."
        placeholder="Engine No."
        :disabled="!canSave"
      />

      <FormulateInput
        type="number"
        name="manufacturing_year"
        label="Year of Manufacture *"
        validationName="Year of Manufacture"
        validation="^required|date:YYYY"
        placeholder="Year of Manufacture"
        :disabled="!canSave"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        v-if="defaultSurcharge != null"
        type="number"
        min="0"
        name="surcharge"
        label="Surcharge %"
        step="any"
        validation="min:0"
        placeholder="Surcharge %"
        :disabled="!canSave"
        :value="defaultSurcharge"
      />
      <FormulateInput
        v-if="defaultDiscount != null"
        type="number"
        min="0"
        name="discount"
        label="Discount %"
        step="any"
        validation="min:0"
        placeholder="Discount %"
        :disabled="!canSave"
        :value="defaultDiscount"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-x-10">
      <FormulateInput
        v-if="defaultNCD != null"
        type="select"
        name="ncd"
        label="NCD"
        placeholder="NCD"
        :options="ncd"
        :disabled="!canSave"
        :value="defaultNCD"
      />
      <FormulateInput
        v-if="hasPassengerTonnage"
        type="number"
        min="0"
        name="passenger_tonnage"
        label="Passenger / Tonnage *"
        placeholder="Passenger / Tonnage"
        validation="required|min:0"
        validationName="Passenger / Tonnage"
        :disabled="!canSave"
      />
      <FormulateInput
        v-if="defaultVehicleUsage != null"
        type="select"
        name="vehicle_usage"
        validationName="Vehicle Usage"
        label="Vehicle Usage *"
        validation="required"
        placeholder="Vehicle Usage"
        @input="getVehicleUsageValue"
        :options="vehicleUsageOptions"
        :disabled="!canSave"
        :value="!isVehicleUsageUploadError ? defaultVehicleUsage : ''"
      />
      <FormulateInput
        v-if="isEndorsementForm"
        type="date"
        name="endorsement_e_date"
        label="Endorsement Effective Date"
        :min="today"
        :disabled="!canSave"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        type="checkbox"
        name="cover_pkg_id"
        label="Cover Package"
        :options="coverPackageOptions"
        @input="changeCovers"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        v-if="mandatoryCovers"
        type="checkbox"
        name="optional_covers"
        label="Optional Covers"
        :options="optionalCovers"
        :value="mandatoryCovers"
        @input="listenToSelectedCovers"
      />
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-1 gap-10">
      <FormulateInput
        type="textarea"
        name="remark"
        label="Remark to Clients"
        placeholder="Remark"
        rows="5"
        :disabled="!canSave"
      />
    </div>
    <div class="text-md font-bold my-3" v-if="subPremium.id === index">
      <span>Sub Premium: {{ subPremium.value   }}</span>
    </div>
    <button v-if="!isEndorsementForm" type="button" class="btn px-2 py-1 btn-primary min-w" @click="$emit('generateSubPremium')">
      Generate Sub Premium
    </button>
    <button v-if="canGenerateEndorsementSubPremium" type="button" class="btn px-2 py-1 btn-primary min-w" @click="$emit('generateSubPremium')">
      Generate Sub Premium
    </button>
  </div>
</template>

<script>

export default {
  props: {
    productCode: {
      type: String,
      default: ''
    },
    index: Number,
    id: [String, Number],
    subPremium: Object,
    ncd: Object,
    productSpecification: String,
    vehicleUsageOptions: Object,
    defaultVehicleUsage: String,
    defaultSurcharge: [String, Number],
    defaultDiscount: [String, Number],
    defaultNCD: [String, Number],

    // For endorsement
    isEndorsement: Boolean,
    vehicleEndorsement: Boolean,
    isNewVehicle: Boolean,
    isEndorsementForm: Boolean,
    hasUploadError: Boolean,
    isNewVehicleDeleted: Boolean,
    isVehicleUsageUploadError: Boolean,
    isVehicleInfoAvailableToEdit: Boolean,

    makes: Array,
    mandatoryCovers: Array,
    coverPkgOptions: Array,
    optionalCvrs: Array,
  },

  data() {
    return {
      selectedMake: null,
      selectedModel: null,
      selectedVehicleUsage: null,
      selectedCoverPackage: [],
      models: {},
      optionalCovers: [],
      coverPackageOptions: [],
      isVehicleValueRequired: false,
      selectedCovers: []
    }
  },
  computed: {
    hasPassengerTonnage() {
      return this.productSpecification === 'TONNAGE' || this.productSpecification === 'PASSENGER'
    },
    canSave() {
      // if not an endorsement
      if (!this.isEndorsement) return true
      // if endorsement type is VEHICLE and is new vehicle
      return this.vehicleEndorsement && this.isNewVehicle
    },
    today() {
      var today = new Date()
      var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()
      return date
    },
    canRemoveErrorMsg(){
        if(this.selectedMake && this.selectedModel && this.selectedVehicleUsage) return true
        return false
    },
    canGenerateEndorsementSubPremium(){
        if(this.vehicleEndorsement && this.isNewVehicle && !this.isNewVehicleDeleted)
            return true;
    },
  },
  watch: {
    // When edit, first item trigger change
    productCode() {
      this.listModelsByMake(this.selectedMake)

      if (!this.id) {
        this.listProductCovers()
      }
      this.listCoverPackages()

      if (this.id) {
        this.changeCovers(this.selectedCoverPackage)
      }
    },
    // Trigger disable cover package and optional covers
    canSave(val) {
      if (val == false) {
        this.listCoverPackages()
        this.listProductCovers()
      }
    }
  },
  methods: {
    listModelsByMake(value) {
      this.selectedMake = value
      this.removeErrorMsg()
      axios.get('/auto-service/list-models-by-product-and-make/' + this.productCode + '/' + value).then(response => {
        this.models = response.data
      })
    },
    listProductCovers() {
        this.optionalCovers = _.cloneDeep(this.optionalCvrs)

        if (!this.canSave) {
            this.optionalCovers.map(item => item.disabled = true)
        } else {
            this.optionalCovers.map(item => item.disabled = false)
        }
        this.listenToSelectedCovers()
    },
    listCoverPackages() {
      this.coverPackageOptions = _.cloneDeep(this.coverPkgOptions)

      if (!this.canSave) {
        this.coverPackageOptions.map(item => item.disabled = true)
      } else {
        this.coverPackageOptions.map(item => item.disabled = false)
      }
    },
    changeCovers(packages) {
        let isPackageSelected = false;
        let selectedPackageIndex = 0;
        for(const key in packages){
            if(packages[key] == this.coverPackageOptions[0]?.value){
                selectedPackageIndex = key
                isPackageSelected = true
            }
        }

        /**
         * handle errors when users choose package and then change the product code
         */
        if(isPackageSelected){
            this.selectedCoverPackage = this.coverPackageOptions[0].value
            /**
             * If it is a selected cover package, the vehicle value is required because at least one among covers requires vehicle value
             */
            this.isVehicleValueRequired = true;
            axios.get(`/auto-service/list-remain-covers/${packages[selectedPackageIndex]}/${this.productCode}`).then(response => {
                this.optionalCovers = response.data
                // Disable covers
                if (!this.canSave) {
                    this.optionalCovers.map(item => item.disabled = true)
                }
            }).then(() => {
                this.disableOtherCoverPackages(this.selectedCoverPackage)
            })
        }
        else{
            this.selectedCoverPackage = []
            axios.get(`/auto-service/list-remain-covers/${packages[selectedPackageIndex+1]}/${this.productCode}`).then(response => {
                this.optionalCovers = response.data
                // Disable covers
                if (!this.canSave) {
                    this.optionalCovers.map(item => item.disabled = true)
                }
            }).then(() => {
                this.disableOtherCoverPackages(this.selectedCoverPackage)
            })
            this.listenToSelectedCovers();
        }
    },

    listenToSelectedCovers(covers = null){
        if(covers)
            this.selectedCovers = covers
        /**
         * If it is a selected cover package, vehicle value is required because at least one among covers requires vehicle value
         */
        if(this.selectedCoverPackage.length == 0){
            let needToUpdateVehicleValue = false;
            for(const key in this.optionalCovers){
                if(this.selectedCovers.includes(this.optionalCovers[key].value))
                    if(this.optionalCovers[key].is_vehicle_val_required){
                        needToUpdateVehicleValue = true;
                        break;
                    }
            }
            if(needToUpdateVehicleValue){
                this.isVehicleValueRequired = true;
            } else {
                this.isVehicleValueRequired = false;
            }
        }
    },

    disableOtherCoverPackages(coverPackageArr) {
      if (coverPackageArr.length === 1) {
        this.coverPackageOptions.filter(item => item.value != coverPackageArr[0])
          .map(item => item.disabled = true)
      } else if (coverPackageArr.length === 0) {
        this.coverPackageOptions.map(item => item.disabled = false)
      }
    },

    getModelValue(value){
        this.selectedModel = value
        this.removeErrorMsg()
    },

    getVehicleUsageValue(value){
        this.selectedVehicleUsage = value
        this.removeErrorMsg()
    },

    removeErrorMsg(){
        if(this.canRemoveErrorMsg && this.hasUploadError){
            let vehicleId = document.querySelector('.vehicle-' + this.index)
            vehicleId.innerHTML= '';
        }
    },
  },

  created() {
    this.listProductCovers()
    this.listCoverPackages()
  },
}
</script>

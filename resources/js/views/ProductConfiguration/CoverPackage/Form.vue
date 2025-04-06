<template>
    <div>
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                <span v-if="id">Edit</span>
                <span v-else>Create</span>
                Cover Package
            </h2>
        </div>
        <div class="grid grid-cols-12 mt-5">
            <div class="intro-y box col-span-12 lg:col-span-6 p-5">
                <div class="grid grid-cols-12 gap-x-10 gap-y-4">
                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Product *</label>
                        <Dropdown
                            class="w-full p-inputtext-sm"
                            v-model="formValues.product_code"
                            :options="autoProductOptions"
                            optionLabel="label"
                            optionValue="value"
                            @change="changeCovers"
                            placeholder="Product"
                            :disabled="id"
                        >
                            <template #option="slotProps">
                                <p class="text-sm font-semibold">{{slotProps.option.label}}</p>
                                <span class="text-xs">{{slotProps.option.desc}}</span>
                            </template>
                        </Dropdown>
                        <span v-if="errors.product_code" class="text-theme-6">
                            {{ errors.product_code[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Name *</label>
                        <InputText
                            class="w-full p-inputtext-sm"
                            v-model="formValues.name"
                            placeholder="Name"
                        />
                        <span v-if="errors.name" class="text-theme-6">
                            {{ errors.name[0] }}
                        </span>
                    </div>

                    <div class="col-span-12">
                        <label class="mb-1 block font-bold">Description</label>
                        <Textarea
                            class="w-full"
                            v-model="formValues.description"
                            rows="3"
                            placeholder="Description"
                        />
                        <span v-if="errors.description" class="text-theme-6">
                            {{ errors.description[0] }}
                        </span>
                    </div>

                    <div v-if="formValues.product_code" class="col-span-12">
                        <label class="mb-1 block font-bold">Covers</label>
                        <div v-for="cover in coverOptions" :key="cover.value" class="mb-2">
                            <Checkbox
                                v-model="formValues.cover_package_components"
                                :value="cover.value"
                                :disabled="cover.disabled" 
                                :binary="false"
                            />
                            <label class="ml-2" :class="{ 'text-gray-400': cover.disabled }">
                                {{ cover.label }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-5">
                    <router-link 
                        to="/product-configuration/cover-packages" 
                        class="btn btn-outline-secondary w-24 mr-1"
                    >
                        Cancel
                    </router-link>
                    <button type="button" @click="handleSubmit" class="btn btn-primary w-24">
                        <span v-if="id">Update</span>
                        <span v-else>Create</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                id: this.$route.params.id ?? null,
                formValues: {
                    product_code: null,
                    name: '',
                    description: '',
                    cover_package_components: ''
                },
                selectedCovers: [],
                autoProductOptions: [],
                coverOptions: {},
                errors: {},
            }
        },

        methods: {
            updateSelectedCovers(value) {
                this.selectedCovers = value;
                this.formValues.cover_package_components = this.selectedCovers.join(' ');
            },

            async listAutoProductsWithDesc() {
                try {
                    const response = await axios.get('/cover-service/list-auto-products-with-desc');
                    this.autoProductOptions = response.data;
                } catch (error) {
                    console.error('Error fetching products:', error);
                }
            },

            async listProductCovers() {
                if (!this.formValues.product_code) return;
                try {
                    const response = await axios.get('/cover-packages-service/list-product-covers/' + this.formValues.product_code);
                    this.coverOptions = response.data;
                    const mandatoryCovers = this.coverOptions
                        .filter(cover => cover.disabled)
                        .map(cover => cover.value);
                        
                    this.formValues.cover_package_components = mandatoryCovers;
                } catch (error) {
                    console.error('Error fetching covers:', error);
                }
            },

            async listProductMandatoryCovers() {
                if (!this.formValues.product_code || this.id) return;
                
                try {
                    const response = await axios.get('/cover-packages-service/list-product-mandatory-covers/' + this.formValues.product_code);
                    this.selectedCovers = response.data.map(cover => cover.value);
                    Vue.set(this.formValues, 'cover_package_components', response.data.map(cover => cover.value));
                } catch (error) {
                    console.error('Error fetching mandatory covers:', error);
                }
            },

            async changeCovers() {
                this.selectedCovers = [];
                this.formValues.cover_package_components = '';
                this.coverOptions = {};
                
                if (!this.formValues.product_code) return;
                
                await this.listProductCovers();
                await this.listProductMandatoryCovers();
            },

            async handleSubmit() {
                try {
                    const endpoint = this.id ? `/cover-packages/${this.id}` : '/cover-packages';
                    const method = this.id ? 'put' : 'post';
                    
                    const response = await axios[method](endpoint, this.formValues);
                    
                    if (response.data.success) {
                        notify(response.data.message, 'success', 'bottom-right');
                        this.$router.push({ name: 'CoverPackageIndex' });
                    }
                } catch (err) {
                    if (err.response?.status === 422) {
                        this.errors = err.response.data.errors;
                    } else {
                        notify('Error', 'error', 'bottom-right');
                    }
                }
            },

            async getCoverPackage() {
                if (!this.id) return;
                
                try {
                    const response = await axios.get(`/cover-packages/${this.id}`);
                    const data = response.data;
                    this.formValues = {
                        ...this.formValues,
                        ...data,
                    };
                    this.selectedCovers = data.cover_package_components.split(' ');
                } catch (error) {
                    console.error('Error fetching cover package:', error);
                }
            },
        },

        async mounted() {
            try {
                await this.listAutoProductsWithDesc();
                await this.getCoverPackage();
                
                if (this.formValues.product_code) {
                    await this.listProductCovers();
                }
            } catch (error) {
                console.error('Error in mounted:', error);
            }
        }
    }
</script>

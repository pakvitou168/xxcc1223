<template>
    <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
        <h4 class="text-lg">Edit Policy</h4>
    </div>
    <div class="mt-4 w-full card">
        <TabView v-model:activeIndex="activeTab" @tab-change="changeTab($event.index)">
            <TabPanel v-for="(tab, index) in tabs" :key="'tab_' + index">
                <template #header>
                    <span :class="{ 'border-b-2 pb-1 border-cyan-600': activeTab === index }">{{ tab.title }}</span>
                </template>
                <component :is="tab.component" :key="tab.key" v-if="tabReactive[tab.key]" v-bind="tab.props"
                    @[tab?.event]="tab?.eventHandler" @[tab?.event1]="tab?.event1Handler"></component>
            </TabPanel>
        </TabView>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import Info from './Tabs/Info.vue'
import InsuredPerson from './Tabs/InsuredPerson.vue'
import OptionalExt from './Tabs/OptionalExt.vue'
import { useRoute } from 'vue-router';

const productCode = ref(null)
const route = useRoute()
const activeTab = ref(0)
const tabReactive = reactive({})
const tabs = computed(() => {
    return [
        {
            title: 'Quotation Info.', key: 'info', component: Info, props: {
                dataId: route.params.id
            }, event: 'next', eventHandler: () => { handleNextTab() }, event1: 'loaded', event1Handler: (code) => {
                console.log("loaded product code", code)
                productCode.value = code
            }
        },
        {
            title: 'Insured Persons', key: 'ins', component: InsuredPerson, props: {
                dataId: route.params.id, proCode: productCode.value
            }, event: 'next', eventHandler: () => { handleNextTab() }, event1: 'back', event1Handler: () => { handlePrevTab() }
        },
        {
            title: 'Optional Extension', key: 'opt', component: OptionalExt, props: {
                dataId: route.params.id
            },
            event: 'back', eventHandler: () => { handlePrevTab() }
        }
    ]
});
const handleNextTab = () => {
    changeTab(activeTab.value + 1)
}
const changeTab = (value) => {
    activeTab.value = value
    const currentTab = tabs.value[value]
    tabReactive[currentTab.key] = true
}
const handlePrevTab = () => {
    changeTab(activeTab.value -1)
}
onMounted(() => {
    changeTab(0)
})
</script>
<style>
.p-tabview-nav {
    @apply border-b
}

.p-tabview-nav-link {
    @apply pb-[1px]
}
</style>
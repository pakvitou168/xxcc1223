<template>
    <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
        <h4 class="text-lg">Edit Policy</h4>
    </div>
    <div class="mt-4 w-full card">
        <div v-if="state.loading" class="flex bg-white py-10">
            <LoadingIndicator />
        </div>
        <TabView v-else v-model:activeIndex="activeTab" @tab-change="changeTab($event.index)">
            <TabPanel v-for="(tab, index) in filteredTabs" :key="tab.title">
                <template #header>
                    <div :class="{ 'border-b-2 pb-1 border-cyan-600': activeTab === index }">{{ tab.title }}</div>
                </template>
                <component :is="tab.component" v-if="tabReactive[tab.key]" @[tab?.event]="tab?.eventHandler"
                    @[tab?.event1]="tab?.event1Handler" v-bind="tab.props">
                </component>
            </TabPanel>
        </TabView>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import Info from './Tabs/Info.vue';
import InsuredPerson from './Tabs/InsuredPerson.vue';
import Commission from './Tabs/Commission.vue'
import Reinsurance from './Tabs/Reinsurance.vue'
import Config from './Tabs/Config.vue'
import Endorsement from './Tabs/Endorsement.vue';
import { useRoute } from 'vue-router';
import endorsementService from '@/services/pa/endorsement.service';
import LoadingIndicator from '@/components/LoadingIndicator.vue'

const policy = ref({})
const route = useRoute()
const dataId = route.params.id
const activeTab = ref(0)
const tabReactive = reactive({})
const state = reactive({
    loading: true
})
const tabs = computed(() => [
    { title: 'Policy Info.', key: 'info', component: Info, props: { dataId, editable: isEndorsementInfo.value }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) } },
    { title: 'Insured Person', key: 'insured', visible: isEndorsementAddDel.value, component: InsuredPerson, props: { dataId }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) } },
    { title: 'Configuration', key: 'conf', component: Config, props: { dataId }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) } },
    { title: 'Commission', key: 'cms', component: Commission, props: { dataId, editable: false }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) }, event1: 'back', event1Handler: () => { changeTab(activeTab.value - 1) } },
    { title: 'Reinsurance', key: 'reins', component: Reinsurance, props: { dataId, productCode: policy.value.product_code, editable: false }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) }, event1: 'back', event1Handler: () => { changeTab(activeTab.value - 1) } },
    { title: 'Endorsement Info.', key: 'endor', component: Endorsement, props: { dataId, productCode: policy.value.product_code }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) }, event1: 'back', event1Handler: () => { changeTab(activeTab.value - 1) } }
]);
const filteredTabs = computed(() => {
    return tabs.value.filter(item => !item.hasOwnProperty('visible') || item.visible === true)
})
const isEndorsementInfo = computed(() => {
    return policy.value?.endorsement_type === 'GENERAL';
})
const isEndorsementAddDel = computed(() => {
    return policy.value?.endorsement_type === 'ADD/DELETE';
})
const isEndorsementCancel = computed(() => {
    return policy.value?.endorsement_type === 'CANCELLATION';
})
const changeTab = (value) => {
    activeTab.value = value
    const currentTab = filteredTabs.value[value]
    tabReactive[currentTab.key] = true
}
const loadDetail = () => {
    endorsementService.info(dataId).then(res => {
        policy.value = res.data
        changeTab(0)
    }).finally(() => state.loading = false)
}
onMounted(() => {
    loadDetail()
})
</script>
<style>
.p-tabview-nav {
    @apply border-b
}

.p-tabview-nav-link {
    @apply pb-[1px]
}

li.p-tabview-header a.p-tabview-nav-link div {
    @apply font-normal text-sm
}
</style>
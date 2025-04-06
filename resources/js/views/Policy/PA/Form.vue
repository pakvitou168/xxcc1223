<template>
    <div class="flex sm:mt-4 lg:mt-6 xl:mt-8">
        <h4 class="text-lg">Edit Policy</h4>
    </div>
    <div class="mt-4 w-full card">
        <TabView v-model:activeIndex="activeTab" @tab-change="changeTab($event.index)">
            <TabPanel v-for="(tab, index) in tabs" :key="tab.title">
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
import Commission from './Tabs/Commission.vue'
import Reinsurance from './Tabs/Reinsurance.vue'
import Config from './Tabs/Config.vue'
import { useRoute } from 'vue-router';
import policyService from '@/services/pa/policy.service';

const policy = ref({})
const route = useRoute()
const dataId = route.params.id
const activeTab = ref(0)
const tabReactive = reactive({})
const tabs = computed(() => [
    { title: 'Policy Config', key: 'conf', component: Config, props: { dataId }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) } },
    { title: 'Commission', key: 'cms', component: Commission, props: { dataId }, event: 'next', eventHandler: () => { changeTab(activeTab.value + 1) }, event1: 'back', event1Handler: () => { changeTab(activeTab.value - 1) } },
    { title: 'Reinsurance', key: 'reins', component: Reinsurance, props: { dataId, productCode: policy.value.product_code }, event: 'back', eventHandler: () => { changeTab(activeTab.value - 1) } }
]);
const changeTab = (value) => {
    activeTab.value = value
    const currentTab = tabs.value[value]
    tabReactive[currentTab.key] = true
}
const loadDetail = () => {
    policyService.edit(dataId).then(res => {
        policy.value = res.data
    })
}
onMounted(() => {
    changeTab(0)
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
</style>
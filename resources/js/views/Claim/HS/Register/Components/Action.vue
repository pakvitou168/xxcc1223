<template>
    <div class="flex justify-center">
        <a title="View" class="view flex items-center mr-1 text-sm" href="javascript:;" @click="events.onView()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                </path>
            </svg>
        </a>
        <a title="Edit" v-if="canEdit" class="edit flex items-center mr-1 text-blue-900" href="javascript:;"
            @click="events.onEdit()">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                </path>
            </svg>
        </a>

    </div>
</template>
<script setup>
import { computed } from 'vue'
import { hasPermission } from "@/services/auth.service";

const row = computed(() => props.rowData)
const canEdit = computed(() => {
    return hasPermission('HS_CLAIM_REGISTER', 'UPDATE') && row.value?.approve_status == null;
});
const canDelete = computed(() => {
    return hasPermission('HS_CLAIM_REGISTER', 'DELETE') && row.value?.status === 'ACT' && row.value?.approved_status === 'PND' && !row.value?.accepted_status;
})
const props = defineProps({
    rowData: {
        type: Object,
        default: {}
    },
    events: {
        type: Object,
        default: {}
    }
})

</script>
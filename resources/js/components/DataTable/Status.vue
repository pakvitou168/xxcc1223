<template>
    <div>
        <span :class="badgeClass[status] + ' inline-block min-w-[3rem] px-2 text-xs py-1 rounded-xl'">{{ badgeLabel[status] ?? status }}</span>
    </div>
</template>

<script setup>
const props = defineProps({
    status: {
        type: String,
        default: ''
    }
})
const badgeLabel = {
    ACT: 'Active',
    DEL: 'Deleted',
    DBL: 'Disabled',
    BLK: 'Blocked',
    LCK: 'Locked',
    PND: 'Pending',
    APV: 'Approved',
    SBM: 'Submitted',
    REV: 'Revised',
    ACP: 'Accepted',
    DRF: 'Draff',
    PRG: 'In Progress',
    REW: 'Renewed',
    REJ: 'Rejected'
};
const badgeClass = {
    ACT: 'bg-green-300',
    DEL: 'bg-red-400 text-white',
    DBL: 'bg-slate-300',
    BLK: 'bg-black text-white',
    LCK: 'bg-slate-400 text-white',
    PND: 'bg-yellow-400 text-white',
    APV: 'bg-green-400',
    SBM: 'bg-blue-200',
    REV: 'bg-blue-300',
    ACP: 'bg-green-400',
    DRF: 'bg-slate-300',
    PRG: 'bg-white border',
    REW: 'bg-lime-500 text-white',
    REJ: 'bg-red-400'
};
</script>
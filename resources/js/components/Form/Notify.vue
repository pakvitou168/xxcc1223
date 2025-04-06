<template>
    <div>
        <Toast />
    </div>
</template>
<script setup>
import { useToast } from "primevue/usetoast";

const toast = useToast()
class notify {
    constructor(msg, pst) {
        this.msg = msg;
        this.pst = pst;
    }
    info = (msg = '', pst = 'tr') => {
        toast.add({ severity: 'info', summary: 'Info', detail: msg, group: 'tl', life: 3000, position: getPosition(pst) });
    }
    error = (msg = '', pst = 'tr') => {
        toast.add({ severity: 'danger', summary: 'Error', detail: msg, group: 'tl', life: 3000, position: getPosition(pst) });
    }
    warn = (msg = '', pst = 'tr') => {
        toast.add({ severity: 'warn', summary: 'Warning', detail: msg, group: 'tl', life: 3000, position: getPosition(pst) });
    }
}

const getPosition = (pst) => {
    let arrPst = {
        'tl': 'top-left',
        'tr': 'top-right',
        'bl': 'bottom-left',
        'br': 'bottom-right'
    }
    return arrPst[pst] ?? arrPst['tr'];
}
defineExpose({
    notify
})
</script>
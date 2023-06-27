<template>
    <Teleport to="body">
        <div v-if="alerts.length" class="notifier bottom-0 end-0 p-3">

            <b-toast visible static no-auto-hide no-close-button
                     :toast-class="`text-white toast-wrapper fade show bg-${alert.type}`"
                     body-class="text-white"
                     :header-class="{
                        'bg-success text-white': alert.type === 'success',
                        'bg-warning text-white': alert.type === 'warning',
                        'bg-danger text-white': alert.type === 'danger',
                    }"
                     v-for="alert in alerts" :key="alert.id">
                <template v-slot:toast-title>
                    <svg class="bd-placeholder-img rounded mr-2" width="20" height="20"
                         xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false"
                         role="img">
                        <rect width="100%" height="100%" fill="#fff"></rect>
                    </svg>
                    <strong class="mr-auto text-white">&nbsp;</strong>
                    <button type="button" @click="removeImmediately(alert.id)" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </template>
                {{ alert.message }}
            </b-toast>
        </div>
    </Teleport>
</template>

<script setup>
import {computed, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import useAlerts from "../../../../Shared/Composables/Alert";

const alert = computed(() => usePage().props?.flash.alert);
const {addAlert, alerts, removeImmediately} = useAlerts();

watch(alert, (newVal) => {
    if (newVal) {
        addAlert(newVal);
    }
});

</script>

<style>
.notifier {
    position: fixed;
    top: 90px;
    right: 0;
    z-index: 100;
}

.toast-wrapper {
    min-width: 348px;
}
</style>

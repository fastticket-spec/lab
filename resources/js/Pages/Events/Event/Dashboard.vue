<script setup>
import DashboardView from "../../Dashboard/components/DashboardView.vue";

defineProps({
    data: Array,
    attendees: Array
})
</script>

<template>
    <dashboard-view :data="data" :attendees="attendees"/>
</template>

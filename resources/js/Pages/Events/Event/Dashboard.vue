<template>
    <dashboard-view :data="data" :orders="orders" :ticket-chart-data="ticketChartData"/>
</template>

<script setup>
import DashboardView from "../../Dashboard/components/DashboardView.vue";

defineProps({
    data: Array,
    orders: Array,
    ticketChartData: null|Array
})
</script>

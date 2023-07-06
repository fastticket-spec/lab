<script setup>
import DashboardView from "./components/DashboardView.vue";

defineProps({
    data: Array,
    attendees: Array
})
</script>

<template>
    <dashboard-view :data="data" :attendees="attendees"/>
</template>

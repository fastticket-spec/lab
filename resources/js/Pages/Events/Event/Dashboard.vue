<script setup>
import DashboardView from "../../Dashboard/components/DashboardView.vue";

defineProps({
    data: Array,
    attendees: Array,
    eventId: String
})
</script>

<template>
    <dashboard-view :data="data" :attendees="attendees" :event-id="eventId"/>
</template>

<script setup>
import AttendeeComponent from "./components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    sort: String,
    zones: Array
})
</script>

<template>
    <attendee-component :attendees="attendees" :zones="zones" :sort="sort" />
</template>

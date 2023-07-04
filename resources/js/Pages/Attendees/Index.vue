<script setup>
import AttendeeComponent from "./components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    sort: String,
    zones: Array,
    q: String,
})
</script>

<template>
    <attendee-component :attendees="attendees" :zones="zones" :sort="sort" :q="q" />
</template>

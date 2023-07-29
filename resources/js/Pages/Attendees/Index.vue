<script setup>
import AttendeeComponent from "./components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    sort: String,
    filter_by: String,
    zones: Array,
    areas: Array,
    q: String,
})
</script>

<template>
    <attendee-component :attendees="attendees" :zones="zones" :areas="areas" :sort="sort" :filter_by="filter_by" :q="q" />
</template>

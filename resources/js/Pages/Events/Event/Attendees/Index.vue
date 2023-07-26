<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    filter_by: String,
    zones: Array,
    q: String,
    accessLevels: Array,
    errors: Object
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :access-levels="accessLevels" :zones="zones" :sort="sort" :filter_by="filter_by" :q="q" :errors="errors" />
</template>

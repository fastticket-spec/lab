<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    zones: Array,
    q: String,
    accessLevels: Array,
    errors: Object
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :access-levels="accessLevels" :zones="zones" :sort="sort" :q="q" :errors="errors" />
</template>

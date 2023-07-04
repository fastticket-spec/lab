<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    zones: Array,
    q: String
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :zones="zones" :sort="sort" :q="q" />
</template>

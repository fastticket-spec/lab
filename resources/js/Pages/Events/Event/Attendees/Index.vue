<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    zones: Array
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :zones="zones" :sort="sort" />
</template>

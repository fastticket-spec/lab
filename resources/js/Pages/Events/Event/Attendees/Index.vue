<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :sort="sort" />
</template>

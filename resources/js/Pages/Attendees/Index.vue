<script setup>
import AttendeeComponent from "./components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    sort: String
})
</script>

<template>
    <attendee-component :attendees="attendees" :sort="sort" />
</template>

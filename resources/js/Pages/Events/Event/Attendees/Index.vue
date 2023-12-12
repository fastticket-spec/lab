<script setup>
import AttendeeComponent from "../../../Attendees/components/AttendeeComponent.vue";

defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    filter_by: String,
    zones: Array,
    areas: Array,
    q: String,
    errors: Object,
    categories: Array
})
</script>

<template>
    <attendee-component :attendees="attendees" :event-id="eventId" :categories="categories" :zones="zones"  :areas="areas" :sort="sort" :filter_by="filter_by" :q="q" :errors="errors" />
</template>

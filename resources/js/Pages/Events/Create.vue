<template>
    <event-form :code="code" :message="message" :edit-mode="editMode" :event="event" :errors="errors"/>
</template>

<script setup>
import EventForm from "./component/EventForm.vue";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    event: {},
    errors: Object
});
</script>
